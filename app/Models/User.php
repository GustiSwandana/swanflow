<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

#[Fillable(['name', 'email', 'password', 'pin'])]
#[Hidden(['password', 'pin', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the primary owner instance for single-user personal use.
     */
    public static function getPrimaryUser(): self
    {
        $user = self::firstOrCreate(
            ['email' => 'gustiswandana@swanflow.com'],
            [
                'name' => 'Gusti Swandana',
                'password' => bcrypt('password'),
                'pin' => Hash::make('123456'),
            ]
        );

        if (empty($user->pin)) {
            $user->update(['pin' => Hash::make('123456')]);
        }

        return $user;
    }

    /**
     * Determine if the user has a configured security PIN.
     */
    public function hasPin(): bool
    {
        return ! empty($this->pin);
    }

    /**
     * Verify the provided PIN against the user's hashed PIN.
     */
    public function verifyPin(string $pin): bool
    {
        return ! empty($this->pin) && Hash::check($pin, $this->pin);
    }

    /**
     * Get the wallets for the user.
     *
     * @return HasMany<Wallet, $this>
     */
    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class);
    }

    /**
     * Get the categories for the user.
     *
     * @return HasMany<Category, $this>
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    /**
     * Get the transactions for the user.
     *
     * @return HasMany<Transaction, $this>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the biometric credentials (Face ID) for the user.
     *
     * @return HasMany<BiometricCredential, $this>
     */
    public function biometricCredentials(): HasMany
    {
        return $this->hasMany(BiometricCredential::class);
    }

    /**
     * Get the subscriptions for the user.
     *
     * @return HasMany<Subscription, $this>
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the debts and receivables for the user.
     *
     * @return HasMany<Debt, $this>
     */
    public function debts(): HasMany
    {
        return $this->hasMany(Debt::class);
    }

    /**
     * Get the monthly budgets for the user.
     *
     * @return HasMany<Budget, $this>
     */
    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    /**
     * Get the to-do activities for the user.
     *
     * @return HasMany<Todo, $this>
     */
    public function todos(): HasMany
    {
        return $this->hasMany(Todo::class);
    }
}
