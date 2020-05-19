<?php

namespace DR\PSDB;

class PSDBClient {

	const RETRY_TIME = 30;
	const MAX_ATTEMPTS = 10;

	/** @var string */
	protected $_baseUrl;

	private $_curlHandle;

  public function __construct($baseUrl) {
    $this->_baseUrl = $baseUrl;
  }

	public function __destruct() {
		if($this->_curlHandle !== null) {
			echo "Closing the HTTP connection with the PSDB service.\n";
			curl_close($this->_curlHandle);
			$this->_curlHandle = null;
		}
	}

  public function getList($id, $limit = 10, $offset = 0) {
    return $this->request("list/$id?limit=$limit&offset=$offset");
  }

  public function getProgramCard($id, $expanded = false) {
    $qs = '';
    if($expanded) {
      $qs .= 'expanded=true';
    }
    return $this->request("programcard/$id?$qs");
  }

  public function request($path) {
    if($this->_curlHandle == null) {
      $this->_curlHandle = curl_init();
      // Return the transfer when exec is called.
      curl_setopt($this->_curlHandle, CURLOPT_RETURNTRANSFER, true);
    }
    $url = $this->_baseUrl . $path;

    // Set the URL
    curl_setopt($this->_curlHandle, CURLOPT_URL, $url);

    // The API can be somewhat unstable in case we don't get a JSON
    // result we wait and try again (usually errors still has a 200 OK
    // but content type is `text/html`). We'll loop over a number of
    // attempts.
    for ($attempt = 0; $attempt < self::MAX_ATTEMPTS; $attempt++) {
        // Fetch the website.
        $result = curl_exec($this->_curlHandle);

        $info = curl_getinfo($this->_curlHandle);

        // If we didn't succeed, continue with next try.
        if (($info['http_code'] != 200) || (substr($info['content_type'], 0, 16) != 'application/json')) {
            $duration = $attempt * self::RETRY_TIME;
            echo "Error in response. Retrying after {$duration} seconds...\n";
            sleep($duration);

            // Continue with next iteration.
            continue;
        }

        // We succeeded so break out of the loop.
        break;
    }

    if ($result === false) {
        throw new \RuntimeException("Error from the PSDB webservice ($url): " . curl_error($this->_curlHandle));
    }

    $decoded_result = json_decode($result);

    if (is_null($decoded_result)) {
        throw new \RuntimeException("Could not decode JSON response from PSDB ({$url}: " . json_last_error_msg() . ') - response was: ' . var_export($result, true));
    }

    return $decoded_result;
  }
}

?>
