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
     * @var string $version Algorithm version of the encrypted URI.
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
        $this->version = hexdec(substr($this->encryptedUri, 0, self::VERSION_HEX_SIZE));

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
        // Throw an error if the URI is encrypted with an unknown
        // algorithm.
        if (!method_exists($this, "decryptEncryptedUriVersion{$this->version}")) {
            throw new \UnexpectedValueException("Unknown algorithm (version {$this->version})");
        }

        return call_user_func(
            [$this, "decryptEncryptedUriVersion{$this->version}"],
            $this->encryptedUri,
            $this->secret
        );
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
        $payloadOffset = self::VERSION_HEX_SIZE + self::V1_HEADER_HEX_SIZE;
        $payload = substr($encryptedUri, $payloadOffset, $payloadLength);

        $initialVectorOffset = $payloadOffset + $payloadLength;
        $initialVector = substr($encryptedUri, $initialVectorOffset);

        $cipher = hash('sha256', $initialVector . ':' . $secret);

        $uri = openssl_decrypt(
            hex2bin($payload),
            'AES-256-CBC-HMAC-SHA1',
            hex2bin($cipher),
            OPENSSL_RAW_DATA,
            hex2bin($initialVector)
        );

        return $uri;
    }
}
