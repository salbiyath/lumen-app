<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CovidDataController extends Controller{

    private $url = 'https://www.worldometers.info/coronavirus/';

    /**
     * @OA\Get(
     *     path="/latest_covid_data",
     *     operationId="/latest_covid_data",
     *     tags={"yourtag"},
     *     @OA\Parameter(
     *         name="country_name",
     *         in="query",
     *         description="Country name you want to get the data",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns latest COVID-19 data based on country inputted",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function latest_covid_data(Request $request){
        $country_name = $request->has('country_name') ? $request->input('country_name') : 'USA';

        $html = $this->get_web_page($this->url);
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $x = new \DOMXPath($dom);

        $data = [];

        foreach ($x->query("//table[@id='main_table_countries_today']/tbody/tr") as $tr) {
            if(strpos($tr->nodeValue, $country_name)){
                $td = $x->query('./td', $tr);

                $data['country_rank'] = $td->item(0)->nodeValue;
                $data['country_name'] = trim($td->item(1)->nodeValue);
                $data['total_cases'] = $this->parse_the_number($td->item(2)->nodeValue);
                $data['new_cases'] = $this->parse_the_number($td->item(3)->nodeValue);
                $data['total_deaths'] = $this->parse_the_number($td->item(4)->nodeValue);
                $data['new_deaths'] = $this->parse_the_number($td->item(5)->nodeValue);
                $data['total_recovered'] = $this->parse_the_number($td->item(6)->nodeValue);
            }
        }

        return $data;
    }

    /**
     * @OA\Get(
     *     path="/top_ten_covid_case",
     *     operationId="/top_ten_covid_case",
     *     tags={"yourtag"},
     *     @OA\Response(
     *         response="200",
     *         description="Returns top ten COVID-19 case data",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function top_ten_covid_case(){
        $html = $this->get_web_page($this->url);
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $x = new \DOMXPath($dom);

        $datas = [];

        $start = 7;

        foreach ($x->query("//table[@id='main_table_countries_today']/tbody/tr") as $i => $tr) {
            if($i > $start && $i <= $start + 10){
                $td = $x->query('./td', $tr);

                $data = [];
                $data['country_rank'] = $td->item(0)->nodeValue;
                $data['country_name'] = trim($td->item(1)->nodeValue);
                $data['total_cases'] = $this->parse_the_number($td->item(2)->nodeValue);
                $data['new_cases'] = $this->parse_the_number($td->item(3)->nodeValue);
                $data['total_deaths'] = $this->parse_the_number($td->item(4)->nodeValue);
                $data['new_deaths'] = $this->parse_the_number($td->item(5)->nodeValue);
                $data['total_recovered'] = $this->parse_the_number($td->item(6)->nodeValue);

                $datas[] = $data;
            }
        }

        return $datas;
    }

    private function get_web_page($url = '', $option = array(), $getinfo = false) {
        $curl = curl_init($url);

        $option[CURLOPT_RETURNTRANSFER] = 1;
        $option[CURLOPT_SSL_VERIFYPEER] = 0;
        $option[CURLOPT_HEADER] = 0;
        $option[CURLOPT_DNS_USE_GLOBAL_CACHE] = false;
        $option[CURLOPT_FOLLOWLOCATION] = true;
        $option[CURLOPT_USERAGENT] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.63 Safari/537.36';

        curl_setopt_array($curl, $option);

        $content = curl_exec($curl);

        $data = curl_getinfo($curl); // get the CURL info.
        $data['error'] = curl_error($curl); // get error messsage occured
        $data['errno'] = curl_errno($curl); // get error number
        $data['content'] = $content;
        curl_close($curl);
        if ($getinfo)
            // info + content
            return $data;
        else
            // just content
            return $data['content'];
    }

    private function parse_the_number($number = 0){
        $number = trim($number);
        // remove all not number
        $number = preg_replace('/[^0-9]/', '', $number);
        return (int) $number;
    }
}