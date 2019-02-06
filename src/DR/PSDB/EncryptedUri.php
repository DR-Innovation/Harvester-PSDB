<?php

namespace DR\PSDB;

/**
 * Handling of Encrypted URI's.
 *
 * @see /docs/DR-Asset-Encryption-White-Paper-Documentation.docx
 */
class EncryptedUri
{
    /**
     * Size of the version in hex characters.
     */
    const VERSION_HEX_SIZE = 2;

    /**
     * Size of the Version 1 header in hex characters.
     */
    const V1_HEADER_HEX_SIZE = 8;

    /**
     * @var string $encryptedUri The encrypted URI string.
     */
    protected $encryptedUri;

    /**
     * @var int $version Algorithm version of the encrypted URI.
     */
    protected $version;

    /**
     * @var string $secret The shared secret.
     */
    protected $secret;

    /**
     * Construct an encrypted URI object.
     *
     * @param string $encryptedUri
     *   The encrypted URI.
     * @param string|null $secret
     *   (optional) The shared secret used for decryption. If not
     *   supplied will be taken from the `PSDB_VIDEO_SECRET`
     *   environment variable.
     *
     * @throws \RuntimeException
     *   When no secret is set for decrypting URI's in either the
     *   parameter or the environment.
     */
    public function __construct(string $encryptedUri, string $secret = null)
    {
        $this->encryptedUri = $encryptedUri;
        $version = hexdec(substr($this->encryptedUri, 0, self::VERSION_HEX_SIZE));

        if (!is_int($version)) {
            throw new \RuntimeException('Could not decode Encrypted URI version field');
        }

        $this->version = $version;

        // If no secret is supplied we get it from the environment.
        if (!is_string($secret)) {
            $secret = getenv('PSDB_VIDEO_SECRET');
        }

        // If no secret is supplied throw an error.
        if (empty($secret)) {
            throw new \RuntimeException('No secret set for decrypting URI\'s');
        }

        $this->secret = $secret;
    }

    /**
     * The decrypted URI.
     *
     * @return string
     *   The decrypted URI.
     *
     * @throws \UnexpectedValueException
     *   When the URI is encrypted with a unknown algorithm version.
     */
    public function uri() : string
    {
        $decryptMethod = "decryptEncryptedUriVersion{$this->version}";

        // Throw an error if the URI is encrypted with an unknown
        // algorithm.
        if (!method_exists($this, $decryptMethod)) {
            throw new \UnexpectedValueException("Unknown algorithm (version {$this->version})");
        }

        return $this->$decryptMethod($this->encryptedUri, $this->secret);
    }

    /**
     * Decrypts the URI using algorithm version 1.
     *
     * @param string $encryptedUri
     *   The encrypted URI string.
     * @param string $secret
     *   The shared secret for decryption.
     *
     * @return string
     *   The decrypted URI.
     */
    protected function decryptEncryptedUriVersion1(string $encryptedUri, string $secret) : string
    {
        $header = substr($encryptedUri, self::VERSION_HEX_SIZE, self::V1_HEADER_HEX_SIZE);

        $payloadLength = hexdec($header);

        if (!is_int($payloadLength)) {
            throw new \RuntimeException('Could not decode Encrypted URI version 1 header');
        }

        $payloadOffset = self::VERSION_HEX_SIZE + self::V1_HEADER_HEX_SIZE;
        $payload = substr($encryptedUri, $payloadOffset, $payloadLength);

        $initialVectorOffset = $payloadOffset + $payloadLength;
        $initialVector = substr($encryptedUri, $initialVectorOffset);

        $cipher = hash('sha256', $initialVector . ':' . $secret);

        $uri = openssl_decrypt(
            $this->hex2bin($payload),
            'AES-256-CBC-HMAC-SHA1',
            $this->hex2bin($cipher),
            OPENSSL_RAW_DATA,
            $this->hex2bin($initialVector)
        );

        if (!is_string($uri)) {
            throw new \RuntimeException('Could not decrypt encrypted URI');
        }

        return $uri;
    }

    /**
     * Helper for converting hex string data to binary string.
     *
     * Just like standard `hex2bin()` but is guaranteed to return a
     * string or throw an exception instead of `hex2bin()` which
     * return `string|false`.
     *
     * @param string $data
     *   The data as a hex string.
     *
     * @return string
     *   The data as a binary string.
     *
     * @throws \RuntimeException
     *   When the data could not be converted from hex to bin.
     */
    protected function hex2bin(string $data) : string
    {
        $binData = hex2bin($data);

        if (!is_string($binData)) {
            throw new \RuntimeException('Could not decode Encrypted URI version 1');
        }

        return $binData;
    }
}
