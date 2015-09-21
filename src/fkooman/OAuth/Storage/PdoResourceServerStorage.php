<?php

namespace fkooman\OAuth\Storage;

use fkooman\OAuth\ResourceServerStorageInterface;
use fkooman\OAuth\ResourceServer;
use PDO;

class PdoResourceServerStorage implements ResourceServerStorageInterface
{
    /** @var \PDO */
    private $db;

    /** @var string */
    private $prefix;

    public function __construct(PDO $db, $prefix = '')
    {
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db = $db;
    }

    public function getResourceServer($resourceServerId)
    {
        $stmt = $this->db->prepare(
            sprintf(
                'SELECT id, scope, secret FROM %s WHERE id = :id',
                $this->prefix.'resource_servers'
            )
        );

        $stmt->bindValue(':id', $resourceServerId, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (false === $result) {
            return false;
        }

        return new ResourceServer(
            $result['id'],
            $result['scope'],
            $result['secret']
        );
    }

    public static function createTableQueries($prefix)
    {
        $query = array();

        $query[] = sprintf(
            'CREATE TABLE IF NOT EXISTS %s (
                id VARCHAR(255) NOT NULL,
                scope VARCHAR(255) NOT NULL,
                secret VARCHAR(255) NOT NULL,
                PRIMARY KEY (id)
            )',
            $prefix.'resource_servers'
        );

        return $query;
    }

    public function initDatabase()
    {
        $queries = self::createTableQueries($this->prefix);
        foreach ($queries as $q) {
            $this->db->query($q);
        }
        $tables = array('resource_servers');
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
