<?php

class Covid19Data
{
    public function __construct()
    {
    }


    /**
     * Scrape data from Wikipedia
     * @param $loc string the country to load it COVID 19 data if null it will return dat for the World
     * @return array|null the data requested
     */
    public function retrieve_covid_data_wiki($loc = null)
    {
        if ($loc === null) {
            $loc = 'World';
        }
        $loc = str_replace('_', ' ', $loc);
        $loc = strtolower($loc);
        if ($loc === 'usa') {
            $loc = 'united state';
        } elseif ($loc === 'uk') {
            $loc = 'united kingdom';
        } elseif ($loc === 'uae') {
            $loc = 'united arab emirates';
        } elseif ($loc === 'sa') {
            $loc = 'south africa';
        }

        //the link we are scraping the data from
        //this link is not working anymore as of 27 july 2020
        //$url = 'https://en.wikipedia.org/wiki/2019%E2%80%9320_coronavirus_pandemic#covid19-container';

        // working link as of 27 July 2020
        $url = 'https://en.wikipedia.org/wiki/COVID-19_pandemic_by_country_and_territory';
        $html = $this->curl_post($url);

        if ($html === null) {
            return null;
        }

        # Create a DOM parser object
        $dom = new DOMDocument();

        # Parse the HTML from the website.
        # The @ before the method call suppresses any warnings that
        # loadHTML might throw because of invalid HTML in the page.
        @$dom->loadHTML($html);

        $tableData = $dom->getElementById('thetable');
        if ($tableData === null) {
            return null;
        }
        $rowData = $tableData->getElementsByTagName('tr');
        foreach ($rowData as $data) {
            if (stripos($data->nodeValue, $loc) !== false) {
                $colData = $data->getElementsByTagName('td');
                $confirmed = $this->sanitize_numbers($colData[0]->nodeValue);
                $deaths = $this->sanitize_numbers($colData[1]->nodeValue);
                $recovered = $this->sanitize_numbers($colData[2]->nodeValue);
                //existing data is not provided so lets calculate it here
                $existing = $confirmed - ($recovered + $deaths);
                return $this->format_output($loc, $existing, $confirmed, $recovered, $deaths);
            }
        }
        return null;
    }


    /**Scrape data from Worldometers website
     * Note: I think their terms are against scraping data from them
     * @param $loc string name of country to pull data, null return the total case worldwide
     * @return array containing object with keys: 'existing','confirmed','recovered','deaths','date','time'
     */
    public function retrieve_covid_data_worldometers($loc = null)
    {
        if ($loc === null) {
            $loc = 'Total';
        }

        $url = 'https://www.worldometers.info/coronavirus/';
        $html = $this->curl_post($url);

        # Create a DOM parser object
        $dom = new DOMDocument();

        # Parse the HTML from Google.
        # The @ before the method call suppresses any warnings that
        # loadHTML might throw because of invalid HTML in the page.
        @$dom->loadHTML($html);

        $tableData = $dom->getElementById('main_table_countries_today');
        if ($tableData === null) {
            return null;
        }
        $rowData = $tableData->getElementsByTagName('tr');

        foreach ($rowData as $data) {
            if (stripos($data->nodeValue, $loc) !== false) {

                $colData = $data->getElementsByTagName('td');

                return $this->format_output($loc,
                    trim(str_replace(',', '', $colData[7]->nodeValue)),
                    trim(str_replace(',', '', $colData[2]->nodeValue)),
                    trim(str_replace(',', '', $colData[6]->nodeValue)),
                    trim(str_replace(',', '', $colData[4]->nodeValue))
                );

            }
        }
        //if we are here it means we didnt find the country provided
        return null;

    }

    /**
     * retrieve data from Ghana Health service
     * @return array of only Ghana data
     * @deprecated link not showing data in the format again
     */
    public function retrieve_covid_data_ghs()
    {
        # Use the Curl extension to query Ghana health service website
        #  and get back a page of results
        $url = 'https://www.ghanahealthservice.org/covid19/';
        $ch = curl_init();
        $timeout = 30;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $html = curl_exec($ch);
        curl_close($ch);

        # Create a DOM parser object
        $dom = new DOMDocument();

        // Parse the HTML from Ghana health service.
        //sup
        @$dom->loadHTML($html);
        $ghana = [];
        $global = [];
        //echo $html; //debug
        # Iterate over all the <div> tags
        foreach ($dom->getElementsByTagName('div') as $data) {
            //print_r($data->nodeValue);
            if ($data->getAttribute('class') === 'widget-box') {
                //print_r($data->nodeValue);
                if (strpos($data->nodeValue, 'Ghana') !== false) {
                    $res = preg_match('/[Gg]hana\'{0,1}s\s*[Ss]ituation\s*[Ee]xisting\s*([\d,]*)\s*[Cc]onfirmed\s*([\d,]*)\s*[Rr]ecovered\s*([\d,]*)\s*[Dd]eaths{0,1}\s*([\d,]*)\s*as\s*at\s*\W*([\s\S]*)\|\s*([\s\S]*)\s*GMT/', $data->nodeValue, $matches);
                    if ($res) {
                        $ghana = array(
                            'existing' => trim($matches[1]),
                            'confirmed' => trim($matches[2]),
                            'recovered' => trim($matches[3]),
                            'deaths' => trim($matches[4]),
                            'date' => trim($matches[5]),
                            'time' => trim($matches[6])
                        );
                        print_r($matches);
                    }
                    //print_r($ghana);
                } else if (strpos($data->nodeValue, 'Global') !== false) {
                    //print_r($data->nodeValue);
                    $res = preg_match('/[Gg]lobal\s*[Ss]ituation\s*[Ee]xisting\s*([\d,]*)\s*[Cc]onfirmed\s*([\d,]*)\s*[Rr]ecovered\s*([\d,]*)\s*[Dd]eaths{0,1}\s*([\d,]*)\s*as\s*at\s*\W*([\s\S]*)\|\s*([\s\S]*)\s*GMT/', $data->nodeValue, $matches);
                    if ($res) {
                        $global = array(
                            'existing' => trim($matches[1]),
                            'confirmed' => trim($matches[2]),
                            'recovered' => trim($matches[3]),
                            'deaths' => trim($matches[4]),
                            'date' => trim($matches[5]),
                            'time' => trim($matches[6])
                        );
                    }
                    //print_r($global);
                }
            }
        }


        return ['ghana' => $ghana, 'global' => $global];
    }


    /**
     * Remove comma's and trailing spaces and cast to integer
     * @param $val String the value to be sanitize
     * @return int the sanitize number
     */
    private function sanitize_numbers($val)
    {
        return (int)trim(str_replace(',', '', $val));
    }


    /**
     * Use to make http request
     * @param $url string the url to send the request
     * @param $headers array of string. the headers for the http request
     * @param $body array|object associative array of the http body
     * @param string $type request type POST or GET, default is POST
     * @param bool $returnResult , the return type is boolean when false or the result of the request when true
     * @return mixed|null return array of request result when $returnResult=true or boolean when $returnResult=false
     */
    private function curl_post($url, $headers = [], $body = [], $type = 'GET', $returnResult = true)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => $returnResult,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $type,
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $res_info = curl_getinfo($curl);
        curl_close($curl);

        if ($err) {
            // echo 'Error #:' . $err . '<br/>';
            $response = null;
        }
        return $response;
    }


    private function format_output($loc, $confirmed, $existing, $recovered, $deaths, $date = null, $time = null)
    {
        $date = $date ?: date('dS F, Y');
        $time = $time ?: date('H:i');
        return array(
            'country' => $loc,
            'existing' => $existing,
            'confirmed' => $confirmed,
            'recovered' => $recovered,
            'deaths' => $deaths,
            'date' => $date,
            'time' => $time
        );
    }

}