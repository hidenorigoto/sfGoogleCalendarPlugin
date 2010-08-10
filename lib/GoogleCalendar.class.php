<?php
/**
 * GoogleCalendar
 *
 * @package
 * @author goto
 * @copyright Copyright (c) 2010
 * @version $Id$
 * @access public
 */
class GoogleCalendar
{
    private
        $google_account  = '',
        $google_password = '';
    protected
        $client          = null,
        $service         = null,
        $initialized     = false;

    /**
     * GoogleCalendar::__construct()
     *
     * @param string $account  Googleアカウント
     * @param string $password Googleアカウントのパスワード
     */
    public function __construct($account, $password)
    {
        $this->google_account  = $account;
        $this->google_password = $password;

        $this->initialize();
    }

    /**
     * GoogleCalendar::initialize()
     * カレンダー初期化
     *
     * @return boolean
     */
    public function initialize()
    {
        if (!isset($this->google_account) || !isset($this->google_password)) {
            return false;
        }

        $client = $this->getClient();
        if (is_null($client)) {
            return false;
        }

        $service = $this->getCalendarService($client);
        if (is_null($service)) {
            return false;
        }
        $this->initialized = true;

        return true;
    }

    /**
     * GoogleCalendar::getClient()
     * Zend_Gdata_HttpClientオブジェクトを生成する。
     *
     * @return　Zend_Gdata_HttpClient　生成できなかった場合はnull
     */
    public function getClient()
    {
        try {
            $client = Zend_Gdata_ClientLogin::getHttpClient(
                $this->google_account,
                $this->google_password,
                Zend_Gdata_Calendar::AUTH_SERVICE_NAME);

            if ($client instanceof Zend_Gdata_HttpClient) {
                return $this->client = $client;
            }
        } catch (Exception $e) {
        }

        return null;
    }

    /**
     * GoogleCalendar::getCalendarService()
     * Zend_Gdata_Calendarオブジェクトを生成する。
     *
     * @param  Zend_Gdata_HttpClient $client
     *
     * @return Zend_Gdata_Calendar 生成できなかった場合はnull
     */
    public function getCalendarService($client)
    {
        if (!($client instanceof Zend_Gdata_HttpClient)) {
            return null;
        }
        $service = new Zend_Gdata_Calendar($client);

        return $this->service = $service;
    }

    /**
     * GoogleCalendar::getEvents()
     *
     * @param Zend_Gdata_Calendar_EventQuery $query       カスタムクエリ
     * @param int                            $start_date  取得範囲の開始日付
     * @param int                            $end_date    取得範囲の終了日付
     * @param bool                           $only_future 未来のみフラグ
     *
     * @return Zend_Gdata_App_Feed
     */
    public function getEvents(
        Zend_Gdata_Calendar_EventQuery $query = null,
        $start_date  = null,
        $end_date    = null,
        $only_future = false
    )
    {
        if (!$this->initialized) {
            return null;
        }
        if (!($query instanceof Zend_Gdata_Calendar_EventQuery)) {
            $query = $this->service->newEventQuery();
            $query->setUser('default')
                ->setVisibility('private')
                ->setProjection('full')
                ->setOrderby('starttime')
                ->setSortorder('ascend');

            if (isset($start_date)) {
                $query->setStartMin(
                    date('Y-m-d H:i:s',
                        DateTimeUtil::long2unix($start_date) - 32400
                    )
                );
            }
            if (isset($end_date)) {
                $query->setStartMax(
                    date('Y-m-d H:i:s',
                        DateTimeUtil::long2unix($end_date) - 32400
                    )
                );
            }
            if ($only_future) {
                $query->setFutureEvents(true);
            }
        }

        return $this->service->getCalendarEventFeed($query);
    }
}
