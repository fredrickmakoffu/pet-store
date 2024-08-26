<?php

namespace App\Guards;

use App\Contracts\Auth\AuthTokenInterface;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Lcobucci\JWT\Token\Plain as PlainToken;

class JwtGuard implements Guard
{
    use GuardHelpers, Macroable;

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected PlainToken|null $token;

    /**
     * Create a new authentication guard.
     *
     * @param  \Illuminate\Contracts\Auth\UserProvider $provider
     * @param  \Illuminate\Contracts\Auth\AuthTokenInterface  $authToken
     * @return void
     */
    public function __construct(UserProvider $provider, protected readonly AuthTokenInterface $authToken)
    {
      $this->provider = $provider;
      $this->token = null;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user(): ?Authenticatable
    {
      // If we've already retrieved the user for the current request we can just
      // return it back immediately. We do not want to fetch the user data on
      // every call to this method because that would be tremendously slow.
      if (! is_null($this->user)) {
        return $this->user;
      }

      // get token from request
      $token = $this->setTokenFromRequest();

      // get user from token
      $user_id =  $this->authToken->retrieve($token);

      // set user
      $this->user = $this->provider->retrieveById($user_id);

      // return user
      return $this->user;
    }

		/**
		 * Determine if the guard has a user instance.
		 *
		 * @return bool
		 */

		public function hasUser()
		{
			return ! is_null($this->user());
		}

		/**
		 * Validate a user's credentials.
		 *
		 * @return \Illuminate\Http\Request
		 */
		public function validate(array $credentials = []): bool
		{
			$user = $this->provider->retrieveByCredentials($credentials);

			return $this->hasValidCredentials($user, $credentials);
		}

		/**
		 * Get the request bearer token
		 *
		 * @return \Illuminate\Http\Request
		 */

		public function setTokenFromRequest(): string|null
		{
			return request()->bearerToken() ?? null;
		}


		/**
     * Attempt to authenticate the user using the given credentials and return the token.
     *
     * @param  array  $credentials
     */
    public function attempt(array $credentials = [], bool $login = true): bool|PlainToken
    {
    	// get user
      $user = $this->provider->retrieveByCredentials($credentials);

      // check if user has valid credentials
      if ( !$this->hasValidCredentials($user, $credentials)) return false;

      // set user
      $this->setUser($user);

      // set token
      $this->token = $this->authToken->create($user);

      // return token or false
      if($this->token === null) return false;

      // return token
      return $this->token->toString();
    }

    /**
     * Determine if the user matches the credentials.
     *
     * @param  array  $credentials
     */
    protected function hasValidCredentials(Authenticatable|null $user, array $credentials): bool
    {
      return $user !== null && $this->provider->validateCredentials($user, $credentials);
    }

    public function getToken(): string|null
    {
      return $this->token->toString();
    }
}
