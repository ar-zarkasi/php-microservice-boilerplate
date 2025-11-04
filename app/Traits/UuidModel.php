<?php
namespace App\Traits;

use Ramsey\Uuid\Uuid;

trait UuidModel
{
    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public function getIncrementing(): bool
    {
        return false;
    }

    /**
     * Get the key type.
     */
    public function getKeyType(): string
    {
        return 'string';
    }

    /**
     * Override the save method to ensure UUID is set before saving.
     */
    public function save(array $options = []): bool
    {
        // Generate UUID if this is a new model and no ID is set
        if (!$this->exists && empty($this->{$this->getKeyName()})) {
            $this->{$this->getKeyName()} = Uuid::uuid4()->toString();
        }

        return parent::save($options);
    }
}