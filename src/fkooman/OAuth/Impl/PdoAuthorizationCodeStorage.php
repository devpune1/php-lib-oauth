<?php

namespace fkooman\OAuth\Impl;

use fkooman\OAuth\AuthorizationCodeStorageInterface;
use fkooman\OAuth\AuthorizationCode;
use fkooman\IO\IO;
use PDO;

class PdoAuthorizationCodeStorage implements AuthorizationCodeStorageInterface
{
    /** @var \PDO */
    private $db;

    /** @var string */
    private $prefix;

    /** @var \fkooman\IO\IO */
    private $io;

    public function __construct(PDO $db, $prefix = '', IO $io = null)
    {
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db = $db;
        if (null === $io) {
            $io = new IO();
        }
        $this->io = $io;
    }

    public function storeAuthorizationCode(AuthorizationCode $authorizationCode)
    {
        $generatedCode = $this->io->getRandom();

        $stmt = $this->db->prepare(
            sprintf(
                'INSERT INTO %s (code, client_id, user_id, issued_at, redirect_uri, scope) VALUES(:code, :client_id, :user_id, :issued_at, :redirect_uri, :scope)',
                $this->prefix.'authorization_codes'
            )
        );
        $stmt->bindValue(':code', $generatedCode, PDO::PARAM_STR);
        $stmt->bindValue(':client_id', $authorizationCode->getClientId(), PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $authorizationCode->getUserId(), PDO::PARAM_STR);
        $stmt->bindValue(':issued_at', $authorizationCode->getIssuedAt(), PDO::PARAM_INT);
        $stmt->bindValue(':redirect_uri', $authorizationCode->getRedirectUri(), PDO::PARAM_STR);
        $stmt->bindValue(':scope', $authorizationCode->getScope(), PDO::PARAM_STR);
        $stmt->execute();

        return $generatedCode;
    }

    public function retrieveAuthorizationCode($authorizationCode)
    {
        $stmt = $this->db->prepare(
            sprintf(
                'SELECT client_id, user_id, issued_at, redirect_uri, scope FROM %s WHERE code = :code',
                $this->prefix.'authorization_codes'
            )
        );
        $stmt->bindValue(':code', $authorizationCode, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (false === $result) {
            return false;
        }

        return AuthorizationCode::fromArray($result);
    }

    public function isFreshAuthorizationCode($authorizationCode)
    {
        $stmt = $this->db->prepare(
            sprintf(
                'SELECT code FROM %s WHERE code = :code',
                $this->prefix.'authorization_codes_log'
            )
        );
        $stmt->bindValue(':code', $authorizationCode, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (false !== $result) {
            return false;
        }

        $stmt = $this->db->prepare(
            sprintf(
                'INSERT INTO %s (code) VALUES(:code)',
                $this->prefix.'authorization_codes_log'
            )
        );
        $stmt->bindValue(':code', $authorizationCode, PDO::PARAM_STR);
        $stmt->execute();

        return true;
    }

    public static function createTableQueries($prefix)
    {
        $query = array();

        $query[] = sprintf(
            'CREATE TABLE IF NOT EXISTS %s (
                code VARCHAR(255) NOT NULL,
                client_id VARCHAR(255) NOT NULL,
                user_id VARCHAR(255) NOT NULL,
                issued_at INT NOT NULL,
                redirect_uri VARCHAR(255) NOT NULL,
                scope VARCHAR(255) NOT NULL,
                PRIMARY KEY (code)
            )',
            $prefix.'authorization_codes'
        );

        $query[] = sprintf(
            'CREATE TABLE IF NOT EXISTS %s (
                code VARCHAR(255) NOT NULL,
                UNIQUE (code)
            )',
            $prefix.'authorization_codes_log'
        );

        return $query;
    }

    public function initDatabase()
    {
        $queries = self::createTableQueries($this->prefix);
        foreach ($queries as $q) {
            $this->db->query($q);
        }
        $tables = array('authorization_codes', 'authorization_codes_log');
        foreach ($tables as $t) {
            // make sure the tables are empty
            $this->db->query(
                sprintf(
                    'DELETE FROM %s',
                    $this->prefix.$t
                )
            );
        }
    }
}
