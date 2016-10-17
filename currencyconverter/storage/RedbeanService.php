<?php namespace Rickbrunken\CurrencyConverter\Storage;

use DateTime;
use R;
use Rickbrunken\CurrencyConverter\Configuration;

/**
 * Description of RedbeanService
 *
 * @author rick.brunken
 */
class RedbeanService extends StorageService
{

    public function __construct(Configuration $configuration)
    {
        parent::__construct($configuration);
        R::setup($configuration->get('connection'));
        R::freeze(true);
    }

    public function install()
    {
        R::freeze(false);

        $currency = R::dispense('currency');
        $currency->currency = 'EUR';
        $currency->rate = 1;
        $currency->datetime = new DateTime();
        R::store($currency);

        $user = R::dispense('user');
        $user->username = 'test';
        $user->password = 'test';
        R::store($user);

        $soapLog = R::dispense('soaplog');
        $soapLog->user = $user;
        $soapLog->datetime = new DateTime();
        R::store($soapLog);

        $collectLog = R::dispense('collectlog');
        $collectLog->datetime = new DateTime();
        $collectLog->provider = '\Rickbrunken\CurrencyConverter\Provider\Kowabunga\KowabungaProvider';
        $collectLog->cost = (float) 1.121231;
        R::store($collectLog);

        R::freeze(true);
        static::flush();
    }

    public function uninstall()
    {
        R::freeze(false);
        R::nuke();
    }

    public function flush()
    {
        R::wipe('currency');
        R::wipe('user');
        R::wipe('soapLog');
        R::wipe('collectLog');
    }

    public function storeCollectLog($provider, $cost, DateTime $datetime = null)
    {
        $o = R::dispense('collectlog');
        $o->provider = $provider;
        $o->cost = $cost;
        $o->datetime = $datetime !== null ? $datetime : new DateTime();
        R::store($o);
    }

    public function storeSoapLog($user = null, DateTime $datetime = null)
    {
        if ($user !== null) {
            $o = R::dispense('soaplog');
            $o->user = $user;
            $o->datetime = $datetime !== null ? $datetime : new DateTime();
            R::store($o);
        }
    }

    public function storeUser($username, $password)
    {
        $o = R::dispense('user');
        $o->username = $username;
        $o->password = $password;
        R::store($o);
    }

    public function storeCurrency($currency, $rate)
    {
        $o = R::dispense('currency');
        $o->currency = $currency;
        $o->rate = $rate;
        $o->datetime = new DateTime();
        R::store($o);
    }

    public function getUserByUsernamePassword($username, $password)
    {
        return R::findOne('user', "username = ? and password = ?", array($username, $password));
    }

    public function getGraphData($currency, DateTime $fromDate, DateTime $toDate)
    {
        $graph = array();
        $result = R::getAll("SELECT strftime('%Y-%m-%d', datetime) as date, AVG(rate) as rate FROM currency WHERE currency = '" . $currency . "' AND datetime >= '" . $fromDate->format('Y-m-d 00:00:00') . "' AND datetime <= '" . $toDate->format('Y-m-d 23:59:59') . "' GROUP BY strftime('%Y-%m-%d', datetime)");
        while ($fromDate < $toDate) {
            $date = $fromDate->format('Y-m-d');
            $graph[$date] = -1;
            $fromDate = $fromDate->modify("+1 day");
        }
        foreach ($result as $r) {
            $graph[$r['date']] = $r['rate'];
        }
        return $graph;
    }
}