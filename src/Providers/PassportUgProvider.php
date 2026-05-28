<?php

namespace Gp10devhts\UgVillageLocations\Providers;

use DOMDocument;
use Illuminate\Support\Facades\Http;

class PassportUgProvider implements LocationProviderInterface
{
    protected string $baseUrl = 'https://passports.go.ug/ajax';

    public function getDistricts(): array
    {
        // This is a bit tricky because the districts are often hardcoded or in the initial page.
        // Based on the example seeder, we have a list.
        $districtsHtml = [
            '<option value="98">ABIM</option>', '<option value="68">ADJUMANI</option>', '<option value="23">AGAGO</option>',
            '<option value="116">ALEBTONG</option>', '<option value="85">AMOLATAR</option>', '<option value="109">AMUDAT</option>',
            '<option value="86">AMURIA</option>', '<option value="99">AMURU</option>', '<option value="1">APAC</option>',
            '<option value="2">ARUA</option>', '<option value="100">BUDAKA</option>', '<option value="106">BUDUDA</option>',
            '<option value="69">BUGIRI</option>', '<option value="36">BUGWERI</option>', '<option value="20">BUHWEJU</option>',
            '<option value="110">BUIKWE</option>', '<option value="107">BUKEDEA</option>', '<option value="126">BUKOMANSIMBI</option>',
            '<option value="87">BUKWO</option>', '<option value="117">BULAMBULI</option>', '<option value="101">BULIISA</option>',
            '<option value="3">BUNDIBUGYO</option>', '<option value="29">BUNYANGABU</option>', '<option value="4">BUSHENYI</option>',
            '<option value="70">BUSIA</option>', '<option value="88">BUTALEJA</option>', '<option value="127">BUTAMBALA</option>',
            '<option value="30">BUTEBO</option>', '<option value="118">BUVUMA</option>', '<option value="111">BUYENDE</option>',
            '<option value="102">DOKOLO</option>', '<option value="119">GOMBA</option>', '<option value="5">GULU</option>',
            '<option value="6">HOIMA</option>', '<option value="89">IBANDA</option>', '<option value="7">IGANGA</option>',
            '<option value="90">ISINGIRO</option>', '<option value="8">JINJA</option>', '<option value="91">KAABONG</option>',
            '<option value="9">KABALE</option>', '<option value="10">KABAROLE</option>', '<option value="82">KABERAMAIDO</option>',
            '<option value="25">KAGADI</option>', '<option value="26">KAKUMIRO</option>', '<option value="129">KALAKI</option>',
            '<option value="21">KALANGALA</option>', '<option value="92">KALIRO</option>', '<option value="11">KALUNGU</option>',
            '<option value="32">KAMPALA</option>', '<option value="42">KAMULI</option>', '<option value="74">KAMWENGE</option>',
            '<option value="83">KANUNGU</option>', '<option value="43">KAPCHORWA</option>', '<option value="37">KAPELEBYONG</option>',
            '<option value="130">KARENGA</option>', '<option value="38">KASANDA</option>', '<option value="44">KASESE</option>',
            '<option value="71">KATAKWI</option>', '<option value="75">KAYUNGA</option>', '<option value="132">KAZO</option>',
            '<option value="45">KIBAALE</option>', '<option value="46">KIBOGA</option>', '<option value="13">KIBUKU</option>',
            '<option value="39">KIKUUBE</option>', '<option value="93">KIRUHURA</option>', '<option value="120">KIRYANDONGO</option>',
            '<option value="47">KISORO</option>', '<option value="133">KITAGWENDA</option>', '<option value="48">KITGUM</option>',
            '<option value="94">KOBOKO</option>', '<option value="14">KOLE</option>', '<option value="49">KOTIDO</option>',
            '<option value="50">KUMI</option>', '<option value="40">KWANIA</option>', '<option value="15">KWEEN</option>',
            '<option value="121">KYANKWANZI</option>', '<option value="112">KYEGEGWA</option>', '<option value="76">KYENJOJO</option>',
            '<option value="31">KYOTERA</option>', '<option value="113">LAMWO</option>', '<option value="51">LIRA</option>',
            '<option value="122">LUUKA</option>', '<option value="52">LUWEERO</option>', '<option value="16">LWENGO</option>',
            '<option value="108">LYANTONDE</option>', '<option value="134">MADI-OKOLLO</option>', '<option value="95">MANAFWA</option>',
            '<option value="105">MARACHA</option>', '<option value="53">MASAKA</option>', '<option value="54">MASINDI</option>',
            '<option value="77">MAYUGE</option>', '<option value="55">MBALE</option>', '<option value="56">MBARARA</option>',
            '<option value="17">MITOOMA</option>', '<option value="96">MITYANA</option>', '<option value="57">MOROTO</option>',
            '<option value="135">MOYO</option>', '<option value="58">MPIGI</option>', '<option value="59">MUBENDE</option>',
            '<option value="60">MUKONO</option>', '<option value="41">NABILATUK</option>', '<option value="84">NAKAPIRIPIRIT</option>',
            '<option value="97">NAKASEKE</option>', '<option value="72">NAKASONGOLA</option>', '<option value="123">NAMAYINGO</option>',
            '<option value="33">NAMISINDWA</option>', '<option value="103">NAMUTUMBA</option>', '<option value="18">NAPAK</option>',
            '<option value="61">NEBBI</option>', '<option value="19">NGORA</option>', '<option value="124">NTOROKO</option>',
            '<option value="62">NTUNGAMO</option>', '<option value="22">NWOYA</option>', '<option value="136">OBONGI</option>',
            '<option value="27">OMORO</option>', '<option value="114">OTUKE</option>', '<option value="104">OYAM</option>',
            '<option value="78">PADER</option>', '<option value="34">PAKWACH</option>', '<option value="63">PALLISA</option>',
            '<option value="64">RAKAI</option>', '<option value="28">RUBANDA</option>', '<option value="24">RUBIRIZI</option>',
            '<option value="35">RUKIGA</option>', '<option value="65">RUKUNGIRI</option>', '<option value="137">RWAMPARA</option>',
            '<option value="125">SERERE</option>', '<option value="12">SHEEMA</option>', '<option value="79">SIRONKO</option>',
            '<option value="66">SOROTI</option>', '<option value="73">SSEMBABULE</option>', '<option value="67">TORORO</option>',
            '<option value="80">WAKISO</option>', '<option value="81">YUMBE</option>', '<option value="115">ZOMBO</option>',
        ];

        $results = [];
        foreach ($districtsHtml as $html) {
            if (preg_match('/value="(\d+)">([^<]+)<\/option>/', $html, $matches)) {
                $results[] = [
                    'id' => (int) $matches[1],
                    'name' => $matches[2],
                ];
            }
        }

        return $results;
    }

    public function getCounties(int $districtId): array
    {
        return $this->fetchFromUrl($this->baseUrl.'/load-counties/?district='.$districtId);
    }

    public function getSubCounties(int $countyId): array
    {
        return $this->fetchFromUrl($this->baseUrl.'/load-subcounties/?county='.$countyId);
    }

    public function getParishes(int $subCountyId): array
    {
        return $this->fetchFromUrl($this->baseUrl.'/load-parishes/?subcounty='.$subCountyId);
    }

    public function getVillages(int $parishId): array
    {
        return $this->fetchFromUrl($this->baseUrl.'/load-villages/?parish='.$parishId);
    }

    protected function fetchFromUrl(string $url): array
    {
        $response = Http::get($url);
        if (! $response->successful()) {
            return [];
        }

        $html = $response->body();
        $dom = new DOMDocument;
        @$dom->loadHTML($html);
        $options = $dom->getElementsByTagName('option');
        $results = [];

        foreach ($options as $option) {
            $id = $option->getAttribute('value');
            $name = $option->textContent;

            if (empty($id) || empty($name)) {
                continue;
            }

            $results[] = [
                'id' => (int) $id,
                'name' => trim($name),
            ];
        }

        return $results;
    }
}
