<?php

namespace DR\PSDB;

class PSDBClient {

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

    // Fetch the website.
    $result = curl_exec($this->_curlHandle);

    if ($result === false) {
        throw new \RuntimeException("Error from the PSDB webservice ($url): " . curl_error($this->_curlHandle));
    }

    $decoded_result = json_decode($result);

    if (is_null($decoded_result)) {
        throw new \RuntimeException('Could not decode JSON from PSDB: ' . json_last_error_msg());
    }

    return $decoded_result;
  }
}

?>
