<?php

namespace Database\Seeders;

use App\Models\RefNationality;
use Illuminate\Database\Seeder;

class RefNationalitySeeder extends Seeder
{

    protected $headers = [];

    protected $data = "Sabahan	Sabahan	萨巴	사바	サバ
Bruneian	Brunei	文莱	브루나이	ブルネイ
American	Amerika	美国	미국	アメリカ
Australian	Australia	澳大利亚	호주	オーストラリア
British	Inggeris	英国	영국	イギリス
Irish 	Irish	爱尔兰	아일랜드	アイルランド
Bruneian	Brunei	文莱	브루나이	ブルネイ
Chinese	Cina	中国	중국	中国
Dutch	Belanda	荷兰	네덜란드	オランダ
Filipino	Filipina	菲律宾	필리핀	フィリピン
French	Perancis	法国	프랑스	フランス
German	Jerman	德国	독일	ドイツ
Indonesian	Indonesia	印度尼西亚	인도네시아	インドネシア
Japanese	Jepun	日本	일본	日本
South Korean	Korea Selatan	韩国	대한민국	韓国
Russian	Rusia	俄罗斯	러시아	ロシア
Singaporean	Singapura	新加坡	싱가포르	シンガポール
Taiwanese	Taiwan	台湾	대만	台湾
Thai	Thai	泰国	태국	タイ
Sarawakian	Sarawak	砂拉越	사라왁	サラワク
West Malaysian	Malaysia Barat	西马	서말레이시아	西マレーシア
Swiss	Switzerland	瑞士	스위스	スイス
Canadian	Kanada	加拿大	캐나다	カナダ
Belgian	Belgium	比利时	벨기에	ベルギー
Danish	Danish	丹麦	덴마크	デンマーク
Polish	Poland	波兰	폴란드	ポーランド
Spanish	Sepanyol	西班牙	스페인	スペイン
Italian	Itali	意大利	이탈리아	イタリア
Swedish	Sweden	瑞典	스웨덴	スウェーデン";

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RefNationality::truncate();

        info("START SEED " . __CLASS__);

        $arrData = $this->formatData();

        foreach ($arrData as $key => $arrVal) {
            $arrVal["code"] = str_pad($key + 1, 5, "0", STR_PAD_LEFT);
            $country = RefNationality::create([
                "code" => $arrVal["code"],
                "title" => $arrVal["title"]
            ]);
        }
        info("FINISH SEED " . __CLASS__);
    }



    protected function formatData()
    {
        $lines = explode("\n", trim($this->data));
        $formattedData = [];

        foreach ($lines as $line) {
            $columns = explode("\t", $line);
            $title = $columns[0];
            $langs = ["en", "ms", "zh", "kr", "jp"];
            $langData = [];

            foreach ($columns as $index => $column) {
                $langData[] = [
                    "lang" => $langs[$index],
                    "title" => $column,
                ];
            }

            $formattedData[] = [
                "title" => $title,
                "lang" => $langData,
            ];
        }

        return $formattedData;
    }
}
