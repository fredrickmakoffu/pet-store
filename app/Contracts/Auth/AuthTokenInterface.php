<?php

namespace App\Contracts\Auth;

use App\Models\User;
use Lcobucci\JWT\UnencryptedToken;

interface AuthTokenInterface
{
		/**
		 * Create a new token.
		 *
		 * @param User $user
		 * @return object
		 */

		public function create(User $user): UnencryptedToken;

		/**
		 * Validate a token.
		 *
		 * @param string $token
		 * @return bool
		 */
		public function validate(User $user, string $token): bool;

		/**
		 * Test token expired.
		 *
		 * @param string $token
		 * @return bool
		 */
		public function expired($token): bool;

		/**
		 * Parse token from jet string expired.
		 *
		 * @param string $jwt
		 * @return UnencryptedToken
		 */
		public function parse(string $jwt): UnencryptedToken;

		/**
		 * Parse token from jet string expired.
		 *
		 * @param string $jwt
		 * @return string
		 */
		public function retrieve(string $jwt): string;
}
