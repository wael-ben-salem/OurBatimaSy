<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * DirectMessages
 */
#[ORM\Table(name: 'direct_messages')]
#[ORM\Index(name: 'fk_dm_receiver_idx', columns: ['receiver_id'])]
#[ORM\Index(name: 'idx_messages_sent', columns: ['sent_at'])]
#[ORM\Index(name: 'fk_dm_sender_idx', columns: ['sender_id'])]
#[ORM\Entity]
class DirectMessages
{
    /**
     * @var int
     */
    #[ORM\Column(name: 'message_id', type: 'integer', nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private $messageId;

    /**
     * @var string
     */
    #[ORM\Column(name: 'content', type: 'text', length: 65535, nullable: false)]
    private $content;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'sent_at', type: 'datetime', nullable: false, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private $sentAt = 'CURRENT_TIMESTAMP';

    /**
     * @var bool
     */
    #[ORM\Column(name: 'is_read', type: 'boolean', nullable: false)]
    private $isRead = '0';

    /**
     * @var \Utilisateur
     */
    #[ORM\JoinColumn(name: 'receiver_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: \Utilisateur::class)]
    private $receiver;

    /**
     * @var \Utilisateur
     */
    #[ORM\JoinColumn(name: 'sender_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: \Utilisateur::class)]
    private $sender;

    public function getMessageId(): ?int
    {
        return $this->messageId;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt(\DateTimeInterface $sentAt): static
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function isRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): static
    {
        $this->isRead = $isRead;

        return $this;
    }

    public function getReceiver(): ?Utilisateur
    {
        return $this->receiver;
    }

    public function setReceiver(?Utilisateur $receiver): static
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getSender(): ?Utilisateur
    {
        return $this->sender;
    }

    public function setSender(?Utilisateur $sender): static
    {
        $this->sender = $sender;

        return $this;
    }


}
