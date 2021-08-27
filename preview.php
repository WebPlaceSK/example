<?php 
//a piece of code from the parser
public function get(){
        $domeny = Domeny::where('screen',0)->get();
        foreach($domeny as $domena){
            $file_headers_https = @get_headers('https://'.$domena->domena);
            $file_headers_http = @get_headers('http://'.$domena->domena);
            if(!$file_headers_https || (
                $file_headers_https[0] == 'HTTP/1.1 404 Not Found'
                or $file_headers_https[0] == 'HTTP/1.1 301 Moved Permanently'
                or $file_headers_https[0] == 'HTTP/1.0 301 Moved Permanently'
                or $file_headers_https[0] == 'HTTP/1.1 301 Redirect'
                or $file_headers_https[0] == 'HTTP/1.0 301 Redirect'
                or $file_headers_https[0] == 'HTTP/1.1 302 Found'
                or $file_headers_https[0] == 'HTTP/1.0 302 Found'
                or $file_headers_https[0] == 'HTTP/1.1 302 Moved Temporarily'
                or $file_headers_https[0] == 'HTTP/1.0 302 Moved Temporarily'
                or $file_headers_https[0] == 'HTTP/1.1 400 Bad Request'
                or $file_headers_https[0] == 'HTTP/1.0 400 Bad Request'
                or $file_headers_https[0] == 'HTTP/1.1 401 Unauthorized'
                or $file_headers_https[0] == 'HTTP/1.0 401 Unauthorized'
                or $file_headers_https[0] == 'HTTP/1.1 403 Forbidden'
                or $file_headers_https[0] == 'HTTP/1.0 403 Forbidden'
                or $file_headers_https[0] == 'HTTP/1.1 404 Not Found'
                or $file_headers_https[0] == 'HTTP/1.0 404 Not Found'
                or $file_headers_https[0] == 'HTTP/1.1 500 Internal Server Error'
                or $file_headers_https[0] == 'HTTP/1.0 500 Internal Server Error'
                or $file_headers_https[0] == 'HTTP/1.1 503 Service Unavailable'
                or $file_headers_https[0] == 'HTTP/1.0 503 Service Unavailable'
                )) {
                $http = false;
                $exists = true;

            }else{
                $http = 'https';
                $exists = false;
            }
            if($exists){
                if(!$file_headers_http || (
                        $file_headers_http[0] == 'HTTP/1.1 404 Not Found'
                        or $file_headers_http[0] == 'HTTP/1.1 301 Moved Permanently'
                        or $file_headers_http[0] == 'HTTP/1.0 301 Moved Permanently'
                        or $file_headers_http[0] == 'HTTP/1.1 301 Redirect'
                        or $file_headers_http[0] == 'HTTP/1.0 301 Redirect'
                        or $file_headers_http[0] == 'HTTP/1.1 302 Found'
                        or $file_headers_http[0] == 'HTTP/1.0 302 Found'
                        or $file_headers_http[0] == 'HTTP/1.1 302 Moved Temporarily'
                        or $file_headers_http[0] == 'HTTP/1.0 302 Moved Temporarily'
                        or $file_headers_http[0] == 'HTTP/1.1 400 Bad Request'
                        or $file_headers_http[0] == 'HTTP/1.0 400 Bad Request'
                        or $file_headers_http[0] == 'HTTP/1.1 401 Unauthorized'
                        or $file_headers_http[0] == 'HTTP/1.0 401 Unauthorized'
                        or $file_headers_http[0] == 'HTTP/1.1 403 Forbidden'
                        or $file_headers_http[0] == 'HTTP/1.0 403 Forbidden'
                        or $file_headers_http[0] == 'HTTP/1.1 404 Not Found'
                        or $file_headers_http[0] == 'HTTP/1.0 404 Not Found'
                        or $file_headers_http[0] == 'HTTP/1.1 500 Internal Server Error'
                        or $file_headers_http[0] == 'HTTP/1.0 500 Internal Server Error'
                        or $file_headers_http[0] == 'HTTP/1.1 503 Service Unavailable'
                        or $file_headers_http[0] == 'HTTP/1.0 503 Service Unavailable'
                    )) {
                    $http = false;
                    $exists = true;
                }else{
                    $http = 'http';
                    $exists = false;
                }
            }

            if($exists){
                Domeny::where('id',$domena->id)->update(['screen' => 2]);
                return redirect()->to('/get');
            }
            if(!$http){
                Domeny::where('id',$domena->id)->update(['screen' => 3]);
                return redirect()->to('/get');
            }

            $url = $domena->domena;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/pagespeedonline/v2/runPagespeed?strategy=desktop&screenshot=true&url='.$http.'://'.urlencode($url));
                $result = curl_exec($ch);
                curl_close($ch);

                $response = json_decode($result, true);
                if(array_key_exists('error', $response)){
                    Domeny::where('id',$domena->id)->update(['error' => '"'.$response['error']['code'].'" : '.$response['error']['message'].'"', 'screen' => 4]);
                    return redirect()->to('/get');
                }
                $screenshot = $response['screenshot']['data'];
                if($screenshot == '_9j_4AAQSkZJRgABAQAAAQABAAD_2wBDAAYEBQYFBAYGBQYHBwYIChAKCgkJChQODwwQFxQYGBcUFhYaHSUfGhsjHBYWICwgIyYnKSopGR8tMC0oMCUoKSj_2wBDAQcHBwoIChMKChMoGhYaKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCj_wAARCACzAUADASIAAhEBAxEB_8QAHQABAAEFAQEBAAAAAAAAAAAAAAUCAwQGCAEJB__EAD8QAAEDAgQDBAcFBwMFAAAAAAEAAgMEEQUSITEGE0EiNlFhBxQVcXSBsyMyUpGhFiQzQrHR8BclwTREY5Lh_8QAGgEBAQEBAQEBAAAAAAAAAAAAAAMBAgQFBv_EACkRAQABAwQBAwMFAQAAAAAAAAABAgMRBBIhMUETIlFhY6EFcZGxwfD_2gAMAwEAAhEDEQA_AOqUREBERAREQEREBEWNidUKHDqqrLc4gidLlva-UE2v02QZKL8sovSRjs1PTOqeF2Ujn4S6tmdLWtyQTGQsjY420jeBmD_C_gVe4r9JtTgPEdZhowSaqhpjldURZiL8lk2gtrZgnJ10yNG7tA_TUX5RN6XBKyq9TwsxmNsLmmplaCA7IX3a0kuNngtDbl9nZdRZZdT6Y8CgcQaWtkDIzLIWcu7ANxYuBJGl9OyDraxQfpiL87p_SnQ1FTDTQ4Tib55Tla0cs3N975rZMt3CT7rgCASRZbhwvipxzh7DsUdB6v65AycRcwSZA4XAzDQ6IJRERAREQEREBERAOy4C4h7wYr8ZP9Ry79Oy4C4h7wYr8ZP9RypbcVo9EXhNgSdhqqJvUWxycD8Rxx8x-FyBlwL8xh3F_wAX-HTdV0_DWJU0jIK3BnyPkqHNa7nMaDywDI3Ne1vtGG_noUy3DWUW3zYLyq50MmAOMjIHVTo2V7HARN0LjY2Av53J2WLV4K-jkxCarwWqigw9rW1THVA-ze8BzLm_UHYX_tmTDWkW6P4Xq5BCIuHZmktjmyitYXujc6wNr3sdbaaa-BVQ4SronSw1XDk0cpLwzPWxx5cupvc9AR77HwKZMNJRbkzh6oiqqOhk4cldXTvdC2I1TeY97G5njLfS2t_y3WJiHD9VJPTUdNgslHWSRyTt5tU20kTC5rjqQNC13Xp80yYawi2R_A_EjJXRuwqXOHZTaRh19-ay1tawREQfQ1ERedcREQEREBERAREQLBERB5lHgsaow6iqXSOqKSnldIwRvL4w4uaHZg033AJJt4rKRAtqg0REBERAREQEREBERAOy4C4h7wYr8ZP9Ry79Oy4C4h7wYr8ZP9RypbcVo9eOGZpB6iy9RUTbjH6RMcaydjzTytml5pEjXEMOUMAb2tAALAeZ8VRWcfYnVMax9Lh7Wsc90eWJwyF1s1jm1uABrfbx1Woosw3LZI-L61uIQ1jqWjklipn0gbI17mFjib9nNbqdtDfUFVYlxliWKQYvBVMo-XivJ9ZtGWi8QsxwsdCFrKJgy3d_pAxRxY7lYYHMiZAz-IQxjXEtABdYHUi-5G9yAVZxX0g4xiNTzaqOhfLdxc9sbu3maG66-_5k-S05Ew6mqJ6ht1Rx_i82LUeJmOlbW0s8lSyRrXAGSRuVxLc1r9b2vfy0VjFeNcSxPEKesqIaNssEU8LBHEQMsoIcDrruSPetYRMOct6d6UMefLnfHQvJeZCHRuIc4ttc9rfb8h0Wioi3DBERB9DURF51xERAREQEREBERAREQEREBERAREQEREBERAREQDsuAuIe8GK_GT_Ucu_TsuAuIe8GK_GT_UcqW3FaPREVExERAREQEREBERAREQEREH0NREXnXEREBERAREQEREBERAREQEREBERAREQEREBERAOy4C4h7wYr8ZP9Ry79Oy4C4h7wYr8ZP9RypbcVo9ERUTEREBZ2DYdJileymjcGA9p7j_K0bnzWJyn8l0tuwNLq5QVclFWRVMB7cZuPMdR81zXu2zt7Vs7IuUzdj255_ZRUwvp5pI5GkFj3M23INirN9bLYeI8SgxembNBEYnQSkWO7muF835g_osHCcJdWxTTyzNp6aIXe9zSSR5Abrim57N1fD0XNNHrTbsTujxPX9_HKOCKp4DXuDXB7QbBwFrjxVKq8c8CIiMEREH0NREXnXEREBERAREQFRNKyFmeVwY24FybDU2CrVEsbJWFkjQ5ptcFBbFZTm_2zNG5jc7C9rqt00bbZpGC-2u6sR4dSxMcxkdmOblIzHUf4FTNh0D6flMaGkXyl3atcjxPkEGTz4rNPMZZ2xzb9EfNEx2V8jGu3sXAFRBwR5bG31puSO5a3kNsCTe-6zPZsUjW-tkzyC13Elt7XtoD5rHU0xHnP8sltVA4sDZWOLzZtje_-WKvLEiw6lil5sceWQC2bMVlNAaLXPzN1rl6iIgIiICIiAiIgHZcBcQ94MV-Mn-o5d-nZcBcQ94MV-Mn-o5UtuK0eiIqJiIiDNt_tDvf_AMqPXs7qiSAQxTctngG7_NI8PxAUoqC6B8ReWZS4B9wL3y721Gu19FzVVEdvVbia8U0xmWdgcEFTVQPxI1EWHGQsmkgaHPAG5A6626LAqpRDmLi8sabC41A6adFJ8NT86rbDK8U7W_ZkkjU3vpf3qmsqqGWmxAztHrLmsMZc09CbhuttQRfQ7Beem9VvmKofSvaCxGnovWa8zOcxOPHePxiO0ZHKJD2NWj-bp8ldVimhELS1pcWk3APRX16nxaoxPAiIjkREQfQ1ERedcREQEREBYVbT1UkmelqzCcuWxZmG-9vFZqtVVPHVQOhmBMbtwCR_RBhCHEbn98hNoi3-F_PfR3kB4KyaTF7G2JxXJP8A2-w8N_1VR4foSSS2bX_yu8_PzV72TT5XNLpsrmtaRzDs21v6DVaxbFLigje32hGXHLldyBpvfr1uPyVBpMVs4sxOLra9OCNtOvzXrsAojcfbhpFsondb-qvPwilcxzSJAHBosJHC2VpaLW20JQeCDEDO5zqyMREmzGxa2sba-N7FY5ocVyFvtUHskX5IBv71fGDU3Mkc4zO5jQxwMhsQLW_oqDgFEek-9_4zvLz8kHnq-L5z-_U4b0-w_wDqrkpsSLWNjxGMEA5yYASdbjrppovZcFo5ZzK9shcSTbmutc9bXRuC0bYwwCWwcXX5rrkkAG5v5BBb9VxXLb2lHfqTANFkUUVdHO71qoimhLRlszK4Hr8lZdgVG6NrH89wbcD7Zw3N-h1WfSwMpoGQxZsjBlFzc2Rq6iIsBERAREQDsuAuIe8GK_GT_Ucu_TsuAuIe8GK_GT_UcqW3FaPRW3F3MaNm-PifBXFRMREQCpf2tTDApqSpo6p-JSWDKjnjlhmYG-UC5NrjU2CiEfd47RJ0suK6IrmJmOnq0-qrsRVTRVMbuJx5j_v9Y7qU1ETXAjK2TM653ueivmJpzcwNdppfpZVAgRNa241uQvTI4uvextb5LPdMtmbMUx88fnn8RwpCIio8giIgIrYJ5pAuW21v0KuIPoaiIvOuIiICIiAiIgIiICKiePmwvjuRmaW3HS6jWYXO11_X5jba99P1RzMzHUJVFFQ4bUxlxOISuJaW6g_nvuvDhdTlI9ozAHcW87-K1m6r4SyK3AwxQsY55eWi2Y7lXFjsREQEREBERAREQDsuAuIe8GK_GT_Ucu_TsuAuIe8GK_GT_UcqW3FaPREVE208LHhf1MN4gEgnvKczQ831jyDs-XN_MeSmJDwC-pquUHshEpFPnE5vHeTV1jvYxaafdO1ytSiwSolhikimpXNewOP2wBbfob9Vc_Z6s0HNo8x2b6w25WNbJCeBpaX7ZvJmdERoJzlk5ZykanTPYu8rW6o2bgd2JV4milfTSTF1PIOYzlxiFlm5RuS_PbzAvoVrMOA1c0UUkclKWyZbXmAOoB18N1RT4JVTsa5j6Voc3N252tIHmCg3Rkvo69dyyU8vIYQ4yNdPaS7zdrRuLNA1PQ-IUJVnhWLEi2lvNRGKlbmeJQWycwc9w12yA_8AsLWIUO_A6thiaX0ueR2VoE7fAm5PTYqlmDVLqN1TzKYRhme3ObmOgNreOqDca6TgDmYYykZKIjUO9eNpf4YbJlLSdbXLNBrpqo3h-u4bhoqMVtJTirHandI2Rwd2ZwW9bG5hsRtcm2mkNJw9WM1MlIW6EHnt1B6gbqmfAaqCGSSSWktGwvcGztJ2vaw6oJ7i08GexbcOiX2kJrEnm5DHfpn8r76rS1JS4LVRue1zqfM1nMIEwNwDY2PVRo2WwCIiMfQ1ERedcREQEREBERAREQEREBERAREQEREBERAREQEREA7LgLiHvBivxk_1HLv07LgLiHvBivxk_wBRypbcVo9ERUTeEDwF1JTMwYFoifWntWcS1li2x1Hne2_S6jkQSjm4GZ3WdXiEtFjkYXA3N_eLWXsowMmMx-uAczttNr5bHb52_PyUUs6lxOWmpuQ2Gke2980kLXO3vuUaqm9kCeIQirMIDhJnyhxPS1tN9P1V0jAS5rh7QFyLtsyw0HXfe6HHJ7m1PRBpP3fV2kAeCtYhiklcwsfT0sYuCOVHlI-awCzCRKRnrDGHmxDW_cy6fPNp7leYzAS0AuxBrzbtFrLN13t10uolFrHlh0Gi9REBERB9DURF51xERAREQEREBERAREQEREBERAREQEREBERAREQDsuAuIe8GK_GT_Ucu_TsuAuIe8GK_GT_UcqW3FaPREVExERAWRhFFU4xicOH4bEZ6yZ4jjjBDcziL2ubBY6IKXODJXRO0kbe49xsqRMwhhDtH_d03VxEBERAREQEREH0NREXnXEREBERARFTnb-Ju9t-qCpFTnb-Ju9t-q8bLG5mdr2Fn4g4WQVovMw8QvC9ovdw01OqCpFjYjXU-HUUtXWSCOCJuZzj0CYdWw4jQw1dMXGGVuZpc0tNvcdQsz4btnG7wyUVHNjz5M7M_4cwuqTUQhuYyx5b2vmFrrWLqK2Z4gSDLGCNT2hp_lx-aCeIloEsZLthmGqC4iIgIiICIiAdlwFxD3gxX4yf6jl36dlwFxD3gxX4yf6jlS24rR6IiomIiICIiAiIgIiICIiAiIg-hqIi864iIgIiICjKvBKOqqHTStfzHG5LXkeH9lJogh_2doAbhst73_iHdWn8PYZBCXTGQMYCS98pFhvqp1UTxMnifFK0OY8WcD1CCF9iYY9oLZZMt-k5tdVDCMMex8QkJaSMzRNv4X_P9VlHBMNIsaKE6g6t_zwR-CYa9uV1HCW-FvO6CnG8Hp8XwiTDqkvEDwGktPaAH_Ku4fh9JheGR0UDAyljbkDXG4t53Wa5oc0hwBB6FeSMbIwteAWnomPLczjHhhx0NDE8ujjja466O0-Q6LxuGUbosuQvYTm--Tc667-ZV80VOXEmJpuLK8xjWNDWABo6BGMR2FUTjKXU7TzBZ-p12_sF6zDKNkscjYGh8Ys03Ogvf-pWYiAiIgIiICIiAdlwFxD3gxX4yf6jl36dlwFxD3gxX4yf6jlS24rR6Iiom2zgTAKLHBWmu532JZl5b8u97308luNJ6N8Lq5eXTsrHvtmtzwNPmPNap6PMZocJFf6_OIeaWZLgm9s19vet4pOOsIpJeZT4ixj7Zb8snT5jyX5nX3NZGqqi3u28dZ-I6eW5Ne_jOBvojhcbClrS78PrDb_l81TH6JqaRrHMp6wh7czf3huo01_UKQb6UKZjg5uMNDhftckX1Fjrl8FRD6S6KFjGx4s0BjQxv2VyAABa9vIKPq6n7hmfqwv8ASijJAZHVPdYlzW1LSW2cW66eIKpPoqpBIWGCszC1x6w3S7so_X-6zIvSPQRSuljxVrXu3Ij37Wba3ibr1_pJoXyOe7Fm53Zbu5VibG46eKz1dX9xmavqx_8ASGK1_U6618v_AFDd_BYVZ6NsMo5BHUsq2PIvbng6Xt0HkVMRek2lhibHHjADGgADl3226b-axK3jzCa2US1OJMfIBlzcsjT5BZXd1e32epn6smascZaLxzw5Q4JRUstFz88kpY7mPzC2W_gtOW8-kLHMPxWgo46CoEr2TFzgARYZbdVoy-_-mzdnTxN7O7nvvt6LWdvuERF71H0NREXnXEREBERAREQEREBERAREQEREBERAREQEREBERAOy4C4h7wYr8ZP9RyIqW3FaPREVExERAREQEREBERAREQEREH__2Q=='){
                    Domeny::where('id',$domena->id)->update(['screen' => 5]);
                    return redirect()->to('/get');
                }
                $screenshot = str_replace(array('_','-'), array('/','+'), $screenshot);

                echo "<img src=\"data:image/jpeg;base64,{$screenshot}\" alt=\"Screenshot\" />";
                $base_to_php = explode(',', $screenshot);
                $data = base64_decode($base_to_php[0]);
                $filepath = storage_path("app\public\\".$url.".jpg");
                file_put_contents($filepath,$data);
                Domeny::where('id',$domena->id)->update(['screen' => 1]);

        }
        return redirect()->to('/get');

    }





//a piece of code to verify that the email has been opened 


    public function open($zoznam, $id, $sid, $mail, $subor){
        $ssis = Send::where([['odosielatel', '=', $id], ['subor', '=', $subor]])->first();
        $sid = $ssis->id;
        $cid = Kontrola::where([
            ['prijemca', '=', $mail],
            ['send_id', '=', $sid],
            ['majtel', '=', $id],
            ['subor', '=', $subor]
        ])->count();

        if($cid == 0 ) {
            $cdb = Send::where([['odosielatel', '=', $id], ['subor', '=', $subor]])->first();
            $ndb = ( $cdb->open ) + 1;
            $now = new DateTime();

            Kontrola::insert([
                'majtel' => $id,
                'prijemca' => $mail,
                'subor' => $subor,
                'send_id' => $sid,
                'created_at' => $now
            ]);

            $user = Your_tables::where([
                ['email', '=', $mail],
                ['majtel', '=', $id],
                ['zoznam', '=', $zoznam]
            ])->first();

            if($user->mesto == NULL or $user->stat == NULL or $user->stat_skratka == NULL or $user->region == NULL) {
                $mesto = file_get_contents("https://ipapi.co/".$_SERVER['REMOTE_ADDR']."/city/");
                $kraj = file_get_contents("https://ipapi.co/".$_SERVER['REMOTE_ADDR']."/region/");
                $stat = file_get_contents("https://ipapi.co/".$_SERVER['REMOTE_ADDR']."/country_name/");
                //$stat_skratka = file_get_contents("https://ipapi.co/".$_SERVER['REMOTE_ADDR']."/country/");

                Your_tables::where([
                    ['zoznam', '=', $zoznam],
                    ['majtel', '=', $id],
                    ['email', '=', $mail]
                ])->update([
                    'suhlas' => 1,
                    'login' => $user->login,
                    //'stat_skratka' => $stat_skratka,
                    'stat' => $stat,
                    'mesto' => $mesto,
                    'region' => $kraj
                ]);

            } else {
                $mesto = file_get_contents("https://ipapi.co/".$_SERVER['REMOTE_ADDR']."/city/");
                $kraj = file_get_contents("https://ipapi.co/".$_SERVER['REMOTE_ADDR']."/region/");
                $stat = file_get_contents("https://ipapi.co/".$_SERVER['REMOTE_ADDR']."/country_name/");
                //$stat_skratka = file_get_contents("https://ipapi.co/".$_SERVER['REMOTE_ADDR']."/country/");
                if($user->mesto != $mesto or $user->stat != $stat or /*$user->stat_skratka != $stat_skratka or*/ $user->region != $kraj) {

                    Your_tables::where([
                        ['zoznam', '=', $zoznam],
                        ['majtel', '=', $id],
                        ['email', '=', $mail]
                    ])->update([
                        'suhlas' => 1,
                        'login' => $user->login,
                        //'stat_skratka' => $stat_skratka,
                        'stat' => $stat,
                        'mesto' => $mesto,
                        'region' => $kraj
                    ]);
                }
            }
            Send::where('id', '=', $sid)->update(['open' => $ndb]);
        }else{
            echo "Dakujem";
        }

    }





//retrieving data from multiple db 
    public function index(Request $request){

        if($request->session()->get('bar') == null){
            return redirect()->route('list.bar');
        }
        $class = AppClass::getApp();
        $cargo = Cargo::orderBy('name')->get();
        if(Items::where('bar',$request->session()->get('bar'))->count() > 0){
            $items = DB::table('items')
                ->where([['user',Auth::id()],['bar',$request->session()->get('bar')]])
                ->leftJoin('cargos', 'items.cargo_id', '=', 'cargos.id')
                ->leftJoin('dodavatels', 'items.service_id', '=', 'dodavatels.id')
                ->get([
                    'items.id',
                    "items.cargo_id",
                    "items.service_id",
                    "items.last_inv",
                    "items.weight_last_inv",
                    "items.weight_count",
                    "cargos.name",
                    "cargos.volume",
                    "cargos.alcohol",
                    "cargos.weight_full",
                    "cargos.weight_empty",
                    "cargos.alcohol",
                    "cargos.alcohol",
                    "cargos.ean",
                    "dodavatels.name_dodavatel",
                ]);
        }else{
            $items = null;
        }

        $druhov_tovaru = Items::where('bar',$request->session()->get('bar'))->count();
        $data = [
            'druhov_tovaru' => $druhov_tovaru,
            'cargos' => $cargo,
            'items' => $items,
        ];
        return view('user.index')->with($class)->with($data);
    }





//basic work with products in cart 

class Cart
{
    public $items = null;
    public $address = null;
    public $delivery = null;
    public $totalQty = 0;
    public $totalPrice = 0;

    public function __construct($oldCart)
    {
        if($oldCart) {
            $this->items = $oldCart->items;
            $this->address = $oldCart->address;
            $this->delivery = $oldCart->delivery;
            $this->totalQty = $oldCart->totalQty;
            $this->totalPrice = $oldCart->totalPrice;
        }
    }

    public function add($item, $id) {
        $price = AppClass::getPrice($id)['price'];
        $storedItem = ['qty' => 0, 'price' => $price, 'item' => $item];
        if($this->items) {
            if(array_key_exists($id, $this->items)) {
                $storedItem = $this->items[$id];
            }
        }
        $storedItem['qty']++;
        $storedItem['price'] = Str_Replace(' ', '', $price) * $storedItem['qty'];
        $this->items[$id] = $storedItem;
        $this->totalQty++;
        $this->totalPrice += Str_Replace(' ', '', $price);
    }

    public function deliery($id) {
        $delivery = Deliveryes::where('id', $id)->first();
        $this->delivery = $delivery;
    }

    public function reduceByOne($id){
        $price = AppClass::getPrice($id)['price'];
        $this->items[$id]['qty']--;
        $this->items[$id]['price'] -= Str_Replace(' ', '', $price);
        $this->totalQty--;
        $this->totalPrice -= Str_Replace(' ', '', $price);
        if($this->items[$id]['qty'] <= 0){
            unset($this->items[$id]);
        }
    }

    public function removeItem($id){
        $this->totalQty -= $this->items[$id]['qty'];
        $this->totalPrice -= $this->items[$id]['price'];
        unset($this->items[$id]);
    }

    public function removeDelivery(){
        unset($this->delivery);
    }
}
