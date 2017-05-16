<?php
/**
 * File was created 26.04.2016 17:55
 */

namespace PeekAndPoke\Component\Slumber\Data\Addon\UserRecord;

use PeekAndPoke\Component\Slumber\Annotation\Slumber;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class UserRecord
{
    /**
     * @var string
     *
     * @Slumber\AsString()
     */
    private $userId;

    /**
     * @var string
     *
     * @Slumber\AsString()
     */
    private $name;

    /**
     * @var string
     *
     * @Slumber\AsString()
     */
    private $role;

    /**
     * @var string
     *
     * @Slumber\AsString()
     */
    private $ip;

    /**
     * @var string
     *
     * @Slumber\AsString()
     */
    private $userAgent;

    /**
     * @return string
     */
    public function __toString()
    {
        /** @noinspection MagicMethodsValidityInspection */
        return implode(
            ', ',
            [
                $this->name,
                $this->userId,
                $this->role,
                $this->ip,
                $this->userAgent,
            ]
        );
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     *
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role
     *
     * @return $this
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     *
     * @return $this
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @param string $userAgent
     *
     * @return $this
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }
}
