<?php
/**
 * SSO - Utility library for authentication with SSO-ONESTOP
 *
 * @author      Ade Indra Saputra <adeayobandung@gmail.com>
 * @copyright   2021 Ade Indra Saputra
 * @license     MIT
 * @package     SSO
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace SSO;

use phpCAS;

// ------------------------------------------------------------------------
//  Constants
// ------------------------------------------------------------------------

/**
 * CAS server host address
 */
define('CAS_SERVER_HOST', 'onestopservice.ssdm.polri.go.id');

/**
 * CAS server uri
 */
define('CAS_SERVER_URI', '/cas');

/**
 * CAS server port
 */
define('CAS_SERVER_PORT', 443);

// ------------------------------------------------------------------------
//  CAS Initialization
// ------------------------------------------------------------------------

// ONLY DO THIS IF phpCAS EXISTS (i.e. installing via Composer). Thanks to Fariskhi for noticing the bug.
if (class_exists('phpCAS')) {
  /**
   * Create phpCAS client
   */
  phpCAS::client(CAS_VERSION_2_0, CAS_SERVER_HOST, CAS_SERVER_PORT, CAS_SERVER_URI);

  /**
   * Set no validation.
   */
  phpCAS::setNoCasServerValidation();
}

/**
 * The SSO class is a simple phpCAS interface for authenticating using
 * SSO-UI CAS service.
 *
 * @class     SSO
 * @category  Authentication
 * @package   SSO 
 * @author    Ade Indra Saputra <dev.rozaka.com>
 * @license   MIT
 */
class SSO
{

  /**
   * Authenticate the user.
   *
   * @return bool Authentication
   */
  public static function authenticate() {
    return phpCAS::forceAuthentication();
  }

  /**
   * Check if the user is already authenticated.
   *
   * @return bool Authentication
   */
  public static function check() {
    return phpCAS::checkAuthentication();
  }

  /**
   * Logout from SSO with URL redirection options
   */
  public static function logout($url='') {
    if ($url === '')
      phpCAS::logout();
    else
      phpCAS::logout(['url' => $url]);
  }

  /**
   * Returns the authenticated user.
   *
   * @return Object User
   */
  public static function getUser() {
    $details = phpCAS::getAttributes();

    // Create new user object, initially empty.
    $user = new \stdClass();
    $user->username = phpCAS::getUser();
    $user->email = $details['mail'];
    $user->nama_lengkap = $details['nama_lengkap'];
    $user->jns_kelamin = $details['jns_kelamin'];
    $user->nrp_nik_id = $details['nrp_nik_id'];

    return $user;
  }

  // ----------------------------------------------------------
  // Manual Installation Stuff
  // ----------------------------------------------------------

  /**
   * Sets the path to CAS.php. Use only when not installing via Composer.
   *
   * @param string $cas_path Path to CAS.php
   */
  public static function setCASPath($cas_path) {
    require $cas_path;

    // Initialize CAS client.
    self::init();
  }

  /**
   * Initialize CAS client. Called by setCASPath().
   */
  private static function init() {
    // Create CAS client.
    phpCAS::client(CAS_VERSION_2_0, CAS_SERVER_HOST, CAS_SERVER_PORT, CAS_SERVER_URI);

    // Set no validation.
    phpCAS::setNoCasServerValidation();
  }

  public static function renew()
  {
    phpCAS::renewAuthentication();

    // logout if desired
    if (isset($_REQUEST['session'])) {
        session_unset();
        session_destroy();
        unset($_REQUEST['session']);
        header("Location: ".$_SERVER['PHP_SELF']);
    }
  }

}