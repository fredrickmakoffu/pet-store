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
use Ramsey\Uuid\Uuid;
use App\Models\User;
use App\Models\JwtToken;

class ManageJwtTokens
{
    private Builder $tokenBuilder;
    private DateTimeImmutable $now;
    private string $algo;
    private string $secret;
    private string $timezone;
    private string $expiration_date;
    private Signer $algorithm;
    private $current_timezone;
    private JwtToken $jwt_token;
    private string $app_url;

    public function __construct()
    {
        // configs (data from .env)
        $this->algo         = config('jwt.algo');
        $this->secret       = config('jwt.secret');
        $this->timezone     = config('jwt.timezone');
        $this->expiration_date = config('jwt.expiration_date');
        $this->app_url = config('app.url');

        // lcobucci setup
        $this->tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
        $this->algorithm = SelectHashingAlgorithm::getAlgorithm($this->algo);

        // time setup
        $this->current_timezone = new DateTimeZone($this->timezone);
        $this->now = new DateTimeImmutable('now', $this->current_timezone);

        $this->jwt_token = new JwtToken();
    }

    public function createToken(User $user) {
        $uuid = Uuid::uuid4();

        // generate token
        $token = $this->generateUserToken($user, $uuid);

        // save in databases
        $this->jwt_token->saveToken($user, $token, $uuid, $this->now->modify($this->expiration_date));

        return $token;
    }

    public function validateToken(User $user, string $token, string $uuid) {
        $signingKey   = InMemory::plainText($this->secret);
    
        $token = (new Parser(new JoseEncoder()))->parse($token);
        
        if($this->expiredToken($token)) {
            return false;
        }

        $constraints = [
            new IdentifiedBy($uuid),
            new SignedWith($this->algorithm, $signingKey),
            new HasClaimWithValue('uid', $user->id),
            new HasClaimWithValue('is_admin', $user->is_admin),
        ];
        
        try {
            (new Validator())->assert($token, ...$constraints); 
        } catch (RequiredConstraintsViolated $e) {
            // list of constraints violation exceptions:
            return false;
        }
        
        return true;
    }

    protected function generateUserToken(User $user, string $uuid) : object 
    {
        $signingKey = InMemory::plainText($this->secret);  
        
        return $this->tokenBuilder
            // Configures the issuer (iss claim)
            ->issuedBy($this->app_url)
            // Configures the id (jti claim)
            ->identifiedBy($uuid)
            // Configures the time that the token was issue (iat claim)
            ->issuedAt($this->now)
            // Configures the time that the token can be used (nbf claim)
            ->canOnlyBeUsedAfter($this->now->modify('+1 minute'))
            // Configures the expiration time of the token (exp claim)
            ->expiresAt($this->now->modify($this->expiration_date))
            // Configures a new claim, called "uid"
            ->withClaim('timezone', $this->timezone)
            // Configures a new claim, called "uid"
            ->withClaim('uid', $user->id)
            // Configures a new claim, called "role"
            ->withClaim('is_admin', $user->is_admin)
            // Builds a new token
            ->getToken($this->algorithm, $signingKey);
    }

    public function getDetailsFromToken($token) : JwtToken | null {
        return $this->jwt_token->getDetailsFromToken($token);
    }

    public function expiredToken($token) : bool 
    {
        $exp = $token->claims()->all()['exp'];
        $timezone = $token->claims()->all()['timezone'];
        $expiry_time = $exp->setTimezone(new DateTimeZone($timezone))->format('Y-m-d H:i:s');
        $now = now()->setTimezone(new DateTimeZone($timezone))->format('Y-m-d H:i:s');

        return $expiry_time < $now;
    }
} 
