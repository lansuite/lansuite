<?php

namespace LanSuite;

/**
 * Database connection implementation.
 * Uses prepared statements by default via the MySQLi engine.
 */
class Database
{
    /**
     * Database hostname
     */
    private string $host = '';

    /**
     * Database port
     */
    private int $port = 3306;

    /**
     * Username of the database user
     */
    private string $username = '';

    /**
     * Password of the database user.
     * See $this->$username.
     */
    private string $password = '';

    /**
     * Name of the database we will connect to
     */
    private string $databaseName = '';

    /**
     * Charset we set for the connection
     */
    private string $charset = '';

    /**
     * Database object
     */
    private ?\mysqli $database = null;

    /**
     * Database table prefix
     */
    private string $tablePrefix = '';

    public function __construct($host, $port, $username, $password, $databaseName, $charset) {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->databaseName = $databaseName;
        $this->charset = $charset;
    }

    /**
     * Connects to the database
     */
    public function connect(): bool
    {
        // Set MySQL to throw exceptions.
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $this->database = new \mysqli($this->host, $this->username, $this->password, $this->databaseName, $this->port);

        // We don't need to check if the connection was successful.
        // If it is _not_ successful, an exception will be thrown.
    
        $this->setCharset($this->charset);

        return true;
    }

    /**
     * Sets the table prefix.
     */
    public function setTablePrefix(string $tablePrefix): void
    {
        $this->tablePrefix = $tablePrefix;
    }

    /**
     * Sets the database charset.
     * 
     * See https://www.php.net/manual/en/mysqli.set-charset.php
     * See https://www.php.net/manual/en/mysqlinfo.concepts.charset.php
     */
    public function setCharset($charset): void
    {
        if ($charset) {
            $this->database->set_charset($charset);
        }
    }

    /**
     * Returns a string representing the type of connection used
     */
    public function getHostInfo(): string
    {
        return $this->database->host_info;
    }

    /**
     * Closes a previously opened database connection
     */
    public function disconnect(): bool
    {
        return $this->database->close();
    }

    /**
     * Get MySQL client info
     */
    public function getClientInfo(): string
    {
        return $this->database->client_info;
    }

    /**
     * Returns the version of the MySQL server
     */
    public function getServerInfo(): string
    {
        return $this->database->server_info;
    }

    /**
     * Executes a query (in a prepared statement style).
     * 
     * $query => The SQL statement.
     * 
     * $parameterValues => The values for the query.
     * 
     * TODO: Implement debug time measurement (with query_start and query_stop)
     */
    public function query(string $query, array $parameterValues = []): \mysqli_stmt
    {
        // Replace table prefix
        $query = str_replace('%prefix%', $this->tablePrefix, $query);

        $statement = $this->database->prepare($query);
        // Will throw an exception in the case of failure.
        $statement->execute($parameterValues);

        return $statement;
    }

    /**
     * Executes $query as prepared statement and returns
     * the full result as an array.
     * 
     * In case of an empty result, an empty array is returned.
     */
    public function queryWithFullResult(string $query, array $parameterValues = []): array
    {
        $statement = $this->query($query, $parameterValues);
        $queryResult = $this->getStatementResult($statement);

        if (!$queryResult) {
            return [];
        }

        $data = [];
        while ($row = $queryResult->fetch_assoc()) {
            $data[] = $row;
        }

        // Cleanup
        $this->freeResult($queryResult);
        $this->closeStatement($statement);

        return $data;
    }

    /**
     * Executes $query as prepared statement and returns
     * only the first row.
     * 
     * In case of an empty result, an empty array is returned.
     */
    public function queryWithOnlyFirstRow(string $query, array $parameterValues = []): array
    {
        $statement = $this->query($query, $parameterValues);
        $queryResult = $this->getStatementResult($statement);

        if (!$queryResult) {
            return [];
        }

        $row = $queryResult->fetch_assoc();

        // Cleanup
        $this->freeResult($queryResult);
        $this->closeStatement($statement);

        return $row;
    }

    /**
     * Returns the total number of rows changed, deleted, inserted, or matched by the last statement executed
     */
    public function getStatementAffectedRows(\mysqli_stmt $statement): int|string
    {
        return $statement->affected_rows;
    }

    /**
     * Get the ID generated from the previous INSERT operation
     */
    public function getStatementInsertID(\mysqli_stmt $statement): int|string
    {
        return $statement->insert_id;
    }

    /**
     * Gets a result set from a prepared statement as a mysqli_result object
     */
    public function getStatementResult(\mysqli_stmt $statement): \mysqli_result|bool
    {
        return $statement->get_result();
    }

    /**
     * Closes a prepared statement
     */
    public function closeStatement(\mysqli_stmt $statement): bool
    {
        return $statement->close();
    }

    /**
     * Gets the number of rows in the result set
     */
    public function getResultNumRows(\mysqli_result $result): int|string
    {
        return $result->num_rows;
    }

    /**
     * Frees the memory associated with a result
     */
    public function freeResult(\mysqli_result $result): void
    {
        $result->free();
    }
}
