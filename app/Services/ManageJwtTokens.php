<?php

namespace App\Services;

use DateTimeImmutable;
use DateTimeZone;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use App\Services\SelectHashingAlgorithm;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Validation\Constraint\IdentifiedBy;
use Lcobucci\JWT\Validation\Validator;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\HasClaimWithValue;
use App\Models\User;
use App\Models\JwtToken;
use App\Contracts\Auth\AuthTokenInterface;
use Exception;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Log;
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Token\UnsupportedHeaderFound;
use Lcobucci\JWT\UnencryptedToken;
use Carbon\Carbon;

class ManageJwtTokens implements AuthTokenInterface
{
	private DateTimeImmutable $now;
	private string $secret;
	private string $timezone;
	private string $expiration_date;
	private Signer $algorithm;
	private User|null $user;
	private string $app_url;

	public function __construct()
	{
		// configs (data from .env)
		$this->secret = config("jwt.secret");
		$this->timezone = config("jwt.timezone");
		$this->expiration_date = config("jwt.expiration_date");
		$this->app_url = config("app.url");

		// lcobucci setup
		$this->algorithm = SelectHashingAlgorithm::getAlgorithm(config("jwt.algo"));
		$this->now = new DateTimeImmutable(
			"now",
			new DateTimeZone(config("jwt.timezone"))
		);

		$this->user = null;
	}

	public function create(User $user): UnencryptedToken
	{
		// get user
		$this->user = $this->setUser($user);

		// generate token
		$token = $this->generate($this->user);

		// save in database
		$jwt_token = new JwtToken();

		// todo: move to actions class
		$jwt_token->saveToken(
			$this->user,
			$token,
			$this->user->uuid,
			$this->now->modify("+ $this->expiration_date minutes")
		);

		return $token;
	}

	public function setUser(User|null $user)
	{
		if($user) return $user;

		if($this->user) return $this->user;

		$credentials = request()->only("email", "password");

		if(!$credentials["email"] || !$credentials["password"]) {
			throw new Exception("Invalid credentials");
		}

		$user = User::where("email", $credentials["email"])->first();

		if(!$user || !password_verify($credentials["password"], $user->password)) {
			throw new Exception("Invalid credentials");
		}

		return $user;
	}

	protected function generate(User $user): UnencryptedToken
	{
		$signingKey = InMemory::plainText($this->secret);
		$tokenBuilder = new Builder(new JoseEncoder(), ChainedFormatter::default());
		$expiry_date = $this->now->modify("+ $this->expiration_date minutes");

		return $tokenBuilder
			// Configures the issuer (iss claim)
			->issuedBy($this->app_url)
			// Configures the subject of the token (sub claim)
			->relatedTo($user->id)
			// Configures the id (jti claim)
			->identifiedBy($user->uuid)
			// Configures the time that the token was issue (iat claim)
			->issuedAt($this->now)
			// Configures the time that the token can be used (nbf claim)
			->canOnlyBeUsedAfter($this->now->modify("+1 minute"))
			// Configures the expiration time of the token (exp claim)
			->expiresAt($expiry_date)
			// Configures a new claim, called "timezone"
			->withClaim("timezone", $this->timezone)
			// Configures a new claim, called "role"
			->withClaim("is_admin", $user->is_admin)
			// Builds a new token
			->getToken($this->algorithm, $signingKey);
	}

	public function parse(string $jwt): UnencryptedToken
	{
		try {
			$parser = new Parser(new JoseEncoder());
			$token = $parser->parse($jwt);
		} catch (CannotDecodeContent | InvalidTokenStructure | UnsupportedHeaderFound $e) {
			Log::info("An invalid token could not be decoded", [$e->getTrace()]);
			throw new Exception("Token could not be parsed");
		}

		return $token;
	}

	public function retrieve(?string $jwt): string
	{
		$token = $this->parse($jwt);
		return $token->claims()->get("sub");
	}

	public function validate(User $user, string $jwt): bool
	{
		try {
			$signingKey = InMemory::plainText($this->secret);

			$token = $this->parse($jwt);

			if ($this->expired($token)) {
				return false;
			}

			$constraints = [
				new IdentifiedBy($user->uuid),
				new SignedWith($this->algorithm, $signingKey),
				new HasClaimWithValue("uid", $user->id),
				new HasClaimWithValue("is_admin", $user->is_admin),
			];
			$validator = new Validator();
			$validator->assert($token, ...$constraints);
		} catch (RequiredConstraintsViolated $e) {
			Log::info("A token could not be validated", [$e->getTrace()]);

			// list of constraints violation exceptions:
			return false;
		}

		return true;
	}

	public function expired($token): bool
	{
		$exp = $token->claims()->all()["exp"];
		$timezone = $token->claims()->all()["timezone"];
		$expiry_time = $exp
			->setTimezone(new DateTimeZone($timezone))
			->format("Y-m-d H:i:s");
		$now = now()
			->setTimezone(new DateTimeZone($timezone))
			->format("Y-m-d H:i:s");

		return $expiry_time < $now;
	}
}
