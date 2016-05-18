<?php

namespace LooplineSystems\CloseIoApiWrapper\Model;

use LooplineSystems\CloseIoApiWrapper\Library\JsonSerializableHelperTrait;
use LooplineSystems\CloseIoApiWrapper\Library\ObjectHydrateHelperTrait;

class Activity implements \JsonSerializable
{
    use ObjectHydrateHelperTrait;
    use JsonSerializableHelperTrait;

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $organization_id;

    /**
     * @var string
     */
    private $voicemail_url;

    /**
     * @var string
     */
    private $updated_by;

    /**
     * @var \DateTime
     */
    private $date_updated;

    /**
     * @var string
     */
    private $recording_url;

    /**
     * @var string
     */
    private $voicemail_duration;

    /**
     * @var string
     */
    private $direction;

    /**
     * @var string
     */
    private $created_by;

    /**
     * @var string
     */
    private $note;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $contact_id;

    /**
     * @var \DateTime
     */
    private $date_created;

    /**
     * @var string
     */
    private $duration;

    /**
     * @var string
     */
    private $user_id;

    /**
     * @var string
     */
    private $lead_id;

    /**
     * @param array $data
     */
    public function __construct(array $data = null)
    {
        if ($data) {
            if (isset($data['_type'])){
                $this->setType($data['_type']);
                unset($data['_type']);
            }

            $this->hydrate($data, []);
        }
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return Activity
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Activity
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrganizationId()
    {
        return $this->organization_id;
    }

    /**
     * @param string $organization_id
     *
     * @return Activity
     */
    public function setOrganizationId($organization_id)
    {
        $this->organization_id = $organization_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getVoicemailUrl()
    {
        return $this->voicemail_url;
    }

    /**
     * @param string $voicemail_url
     *
     * @return Activity
     */
    public function setVoicemailUrl($voicemail_url)
    {
        $this->voicemail_url = $voicemail_url;

        return $this;
    }

    /**
     * @return string
     */
    public function getUpdatedBy()
    {
        return $this->updated_by;
    }

    /**
     * @param string $updated_by
     *
     * @return Activity
     */
    public function setUpdatedBy($updated_by)
    {
        $this->updated_by = $updated_by;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateUpdated()
    {
        return $this->date_updated;
    }

    /**
     * @param \DateTime $date_updated
     *
     * @return Activity
     */
    public function setDateUpdated($date_updated)
    {
        $this->date_updated = $date_updated;

        return $this;
    }

    /**
     * @return string
     */
    public function getRecordingUrl()
    {
        return $this->recording_url;
    }

    /**
     * @param string $recording_url
     *
     * @return Activity
     */
    public function setRecordingUrl($recording_url)
    {
        $this->recording_url = $recording_url;

        return $this;
    }

    /**
     * @return string
     */
    public function getVoicemailDuration()
    {
        return $this->voicemail_duration;
    }

    /**
     * @param string $voicemail_duration
     *
     * @return Activity
     */
    public function setVoicemailDuration($voicemail_duration)
    {
        $this->voicemail_duration = $voicemail_duration;

        return $this;
    }

    /**
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @param string $direction
     *
     * @return Activity
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;

        return $this;
    }

    /**
     * @return string
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * @param string $created_by
     *
     * @return Activity
     */
    public function setCreatedBy($created_by)
    {
        $this->created_by = $created_by;

        return $this;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $note
     *
     * @return Activity
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return Activity
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getContactId()
    {
        return $this->contact_id;
    }

    /**
     * @param string $contact_id
     *
     * @return Activity
     */
    public function setContactId($contact_id)
    {
        $this->contact_id = $contact_id;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->date_created;
    }

    /**
     * @param \DateTime $date_created
     *
     * @return Activity
     */
    public function setDateCreated($date_created)
    {
        $this->date_created = $date_created;

        return $this;
    }

    /**
     * @return string
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param string $duration
     *
     * @return Activity
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param string $user_id
     *
     * @return Activity
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getLeadId()
    {
        return $this->lead_id;
    }

    /**
     * @param string $lead_id
     *
     * @return Activity
     */
    public function setLeadId($lead_id)
    {
        $this->lead_id = $lead_id;

        return $this;
    }
}
