<?php

namespace App\Interface;

interface TimestampableEntityInterface
{
    public function getCreatedAt(): ?\DateTimeInterface;

    public function getUpdatedAt(): ?\DateTimeInterface;

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static;

    public function setCreatedAt(\DateTimeInterface $createdAt): static;
}
