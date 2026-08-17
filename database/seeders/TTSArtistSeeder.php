<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\TTSArtist;
use App\Models\TTSLanguage;
use Illuminate\Database\Seeder;

final class TTSArtistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $artists = [
            /*'af-ZA' => ['female' => ['af-ZA-AdriNeural'], 'male' => ['af-ZA-WillemNeural']],
            'am-ET' => ['female' => ['am-ET-MekdesNeural'], 'male' => ['am-ET-AmehaNeural']],
            'ar-AE' => ['female' => ['ar-AE-FatimaNeural'], 'male' => ['ar-AE-HamdanNeural']],
            'ar-BH' => ['female' => ['ar-BH-LailaNeural'], 'male' => ['ar-BH-AliNeural']],
            'ar-DZ' => ['female' => ['ar-DZ-AminaNeural'], 'male' => ['ar-DZ-IsmaelNeural']],
            'ar-EG' => ['female' => ['ar-EG-SalmaNeural'], 'male' => ['ar-EG-ShakirNeural']],
            'ar-IQ' => ['female' => ['ar-IQ-RanaNeural'], 'male' => ['ar-IQ-BasselNeural']],
            'ar-JO' => ['female' => ['ar-JO-SanaNeural'], 'male' => ['ar-JO-TaimNeural']],
            'ar-KW' => ['female' => ['ar-KW-NouraNeural'], 'male' => ['ar-KW-FahedNeural']],
            'ar-LB' => ['female' => ['ar-LB-LaylaNeural'], 'male' => ['ar-LB-RamiNeural']],
            'ar-LY' => ['female' => ['ar-LY-ImanNeural'], 'male' => ['ar-LY-OmarNeural']],
            'ar-MA' => ['female' => ['ar-MA-MounaNeural'], 'male' => ['ar-MA-JamalNeural']],
            'ar-OM' => ['female' => ['ar-OM-AyshaNeural'], 'male' => ['ar-OM-AbdullahNeural']],
            'ar-QA' => ['female' => ['ar-QA-AmalNeural'], 'male' => ['ar-QA-MoazNeural']],
            'ar-SA' => ['female' => ['ar-SA-ZariyahNeural'], 'male' => ['ar-SA-HamedNeural']],
            'ar-SY' => ['female' => ['ar-SY-AmanyNeural'], 'male' => ['ar-SY-LaithNeural']],
            'ar-TN' => ['female' => ['ar-TN-ReemNeural'], 'male' => ['ar-TN-HediNeural']],
            'ar-YE' => ['female' => ['ar-YE-MaryamNeural'], 'male' => ['ar-YE-SalehNeural']],
            'as-IN' => ['female' => ['as-IN-YashicaNeural'], 'male' => ['as-IN-PriyomNeural']],
            'az-AZ' => ['female' => ['az-AZ-BanuNeural'], 'male' => ['az-AZ-BabekNeural']],
            'bg-BG' => ['female' => ['bg-BG-KalinaNeural'], 'male' => ['bg-BG-BorislavNeural']],*/
            'bn-BD' => ['female' => ['bn-BD-NabanitaNeural'], 'male' => ['bn-BD-PradeepNeural']],
            'bn-IN' => ['female' => ['bn-IN-TanishaaNeural'], 'male' => ['bn-IN-BashkarNeural']],
            /*'bs-BA' => ['female' => ['bs-BA-VesnaNeural'], 'male' => ['bs-BA-GoranNeural']],
            'ca-ES' => ['female' => ['ca-ES-JoanaNeural', 'ca-ES-AlbaNeural'], 'male' => ['ca-ES-EnricNeural']],
            'cs-CZ' => ['female' => ['cs-CZ-VlastaNeural'], 'male' => ['cs-CZ-AntoninNeural']],
            'cy-GB' => ['female' => ['cy-GB-NiaNeural'], 'male' => ['cy-GB-AledNeural']],
            'da-DK' => ['female' => ['da-DK-ChristelNeural'], 'male' => ['da-DK-JeppeNeural']],
            'de-AT' => ['female' => ['de-AT-IngridNeural'], 'male' => ['de-AT-JonasNeural']],
            'de-CH' => ['female' => ['de-CH-LeniNeural'], 'male' => ['de-CH-JanNeural']],
            'de-DE' => ['female' => ['de-DE-KatjaNeural', 'de-DE-SeraphinaMultilingualNeural', 'de-DE-AmalaNeural', 'de-DE-ElkeNeural', 'de-DE-GiselaNeural', 'de-DE-KlarissaNeural', 'de-DE-LouisaNeural', 'de-DE-MajaNeural', 'de-DE-TanjaNeural', 'de-DE-Seraphina:DragonHDLatestNeural'], 'male' => ['de-DE-ConradNeural', 'de-DE-FlorianMultilingualNeural', 'de-DE-BerndNeural', 'de-DE-ChristophNeural', 'de-DE-KasperNeural', 'de-DE-KillianNeural', 'de-DE-KlausNeural', 'de-DE-RalfNeural', 'de-DE-Florian:DragonHDLatestNeural']],
            'el-GR' => ['female' => ['el-GR-AthinaNeural'], 'male' => ['el-GR-NestorasNeural']],
            'en-AU' => ['female' => ['en-AU-NatashaNeural', 'en-AU-AnnetteNeural', 'en-AU-CarlyNeural', 'en-AU-ElsieNeural', 'en-AU-FreyaNeural', 'en-AU-JoanneNeural', 'en-AU-KimNeural', 'en-AU-TinaNeural'], 'male' => ['en-AU-WilliamNeural', 'en-AU-WilliamMultilingualNeural', 'en-AU-DarrenNeural', 'en-AU-DuncanNeural', 'en-AU-KenNeural', 'en-AU-NeilNeural', 'en-AU-TimNeural']],
            'en-CA' => ['female' => ['en-CA-ClaraNeural'], 'male' => ['en-CA-LiamNeural']],
            'en-GB' => ['female' => ['en-GB-SoniaNeural', 'en-GB-LibbyNeural', 'en-GB-AdaMultilingualNeural', 'en-GB-AbbiNeural', 'en-GB-BellaNeural', 'en-GB-HollieNeural', 'en-GB-MaisieNeural', 'en-GB-OliviaNeural'], 'male' => ['en-GB-RyanNeural', 'en-GB-OllieMultilingualNeural', 'en-GB-AlfieNeural', 'en-GB-ElliotNeural', 'en-GB-EthanNeural', 'en-GB-NoahNeural', 'en-GB-OliverNeural', 'en-GB-ThomasNeural']],
            'en-HK' => ['female' => ['en-HK-YanNeural'], 'male' => ['en-HK-SamNeural']],
            'en-IE' => ['female' => ['en-IE-EmilyNeural'], 'male' => ['en-IE-ConnorNeural']],
            'en-IN' => ['female' => ['en-IN-AartiIndicNeural', 'en-IN-NeerjaIndicNeural', 'en-IN-AashiNeural', 'en-IN-AartiNeural', 'en-IN-AnanyaNeural', 'en-IN-KavyaNeural', 'en-IN-NeerjaNeural'], 'male' => ['en-IN-ArjunIndicNeural', 'en-IN-PrabhatIndicNeural', 'en-IN-AaravNeural', 'en-IN-ArjunNeural', 'en-IN-KunalNeural', 'en-IN-PrabhatNeural', 'en-IN-RehaanNeural']],
            'en-KE' => ['female' => ['en-KE-AsiliaNeural'], 'male' => ['en-KE-ChilembaNeural']],
            'en-NG' => ['female' => ['en-NG-EzinneNeural'], 'male' => ['en-NG-AbeoNeural']],
            'en-NZ' => ['female' => ['en-NZ-MollyNeural'], 'male' => ['en-NZ-MitchellNeural']],
            'en-PH' => ['female' => ['en-PH-RosaNeural'], 'male' => ['en-PH-JamesNeural']],
            'en-SG' => ['female' => ['en-SG-LunaNeural'], 'male' => ['en-SG-WayneNeural']],
            'en-TZ' => ['female' => ['en-TZ-ImaniNeural'], 'male' => ['en-TZ-ElimuNeural']],
            'en-US' => [
                'female' => [
                    'en-US-AvaMultilingualNeural', 'en-US-EmmaMultilingualNeural', 'en-US-NovaTurboMultilingualNeural', 'en-US-ShimmerTurboMultilingualNeural',
                    'en-US-AvaNeural', 'en-US-EmmaNeural', 'en-US-JennyNeural', 'en-US-AriaNeural', 'en-US-JaneNeural', 'en-US-LunaNeural', 'en-US-SaraNeural', 'en-US-NancyNeural',
                    'en-US-CoraMultilingualNeural', 'en-US-AmberNeural', 'en-US-AnaNeural', 'en-US-AshleyNeural', 'en-US-CoraNeural', 'en-US-ElizabethNeural',
                    'en-US-JennyMultilingualNeural', 'en-US-MichelleNeural', 'en-US-MonicaNeural', 'en-US-AIGenerate2Neural', 'en-US-AmandaMultilingualNeural',
                    'en-US-EvelynMultilingualNeural', 'en-US-LolaMultilingualNeural', 'en-US-NancyMultilingualNeural', 'en-US-PhoebeMultilingualNeural', 'en-US-SerenaMultilingualNeural',
                    'en-US-Ava:DragonHDLatestNeural', 'en-US-Emma:DragonHDLatestNeural', 'en-US-Emma2:DragonHDLatestNeural', 'en-US-Aria:DragonHDLatestNeural',
                    'en-US-Ava3:DragonHDLatestNeural', 'en-US-Bree:DragonHDLatestNeural', 'en-US-Jane:DragonHDLatestNeural', 'en-US-Jenny:DragonHDLatestNeural',
                    'en-US-Nova:DragonHDLatestNeural', 'en-US-Phoebe:DragonHDLatestNeural', 'en-US-Serena:DragonHDLatestNeural',
                ],
                'male' => [
                    'en-US-AndrewMultilingualNeural', 'en-US-AlloyTurboMultilingualNeural', 'en-US-EchoTurboMultilingualNeural', 'en-US-OnyxTurboMultilingualNeural',
                    'en-US-BrianMultilingualNeural', 'en-US-AndrewNeural', 'en-US-BrianNeural', 'en-US-GuyNeural', 'en-US-DavisNeural', 'en-US-JasonNeural', 'en-US-KaiNeural', 'en-US-TonyNeural',
                    'en-US-ChristopherMultilingualNeural', 'en-US-BrandonMultilingualNeural', 'en-US-BrandonNeural', 'en-US-ChristopherNeural', 'en-US-EricNeural', 'en-US-JacobNeural',
                    'en-US-RogerNeural', 'en-US-RyanMultilingualNeural', 'en-US-SteffanMultilingualNeural', 'en-US-SteffanNeural', 'en-US-AdamMultilingualNeural', 'en-US-AIGenerate1Neural',
                    'en-US-AshTurboMultilingualNeural', 'en-US-DavisMultilingualNeural', 'en-US-DerekMultilingualNeural', 'en-US-DustinMultilingualNeural', 'en-US-LewisMultilingualNeural',
                    'en-US-SamuelMultilingualNeural', 'en-US-Adam:DragonHDLatestNeural', 'en-US-Andrew:DragonHDLatestNeural', 'en-US-Andrew2:DragonHDLatestNeural',
                    'en-US-Brian:DragonHDLatestNeural', 'en-US-Davis:DragonHDLatestNeural', 'en-US-Steffan:DragonHDLatestNeural', 'en-US-Alloy:DragonHDLatestNeural',
                    'en-US-Andrew3:DragonHDLatestNeural',
                ],
                'neutral' => ['en-US-FableTurboMultilingualNeural', 'en-US-BlueNeural', 'en-US-MultiTalker-Ava-Andrew:DragonHDLatestNeural', 'en-US-MultiTalker-Ava-Steffan:DragonHDLatestNeural'],
            ],
            'en-ZA' => ['female' => ['en-ZA-LeahNeural'], 'male' => ['en-ZA-LukeNeural']],
            'es-AR' => ['female' => ['es-AR-ElenaNeural'], 'male' => ['es-AR-TomasNeural']],
            'es-BO' => ['female' => ['es-BO-SofiaNeural'], 'male' => ['es-BO-MarceloNeural']],
            'es-CL' => ['female' => ['es-CL-CatalinaNeural'], 'male' => ['es-CL-LorenzoNeural']],
            'es-CO' => ['female' => ['es-CO-SalomeNeural'], 'male' => ['es-CO-GonzaloNeural']],
            'es-CR' => ['female' => ['es-CR-MariaNeural'], 'male' => ['es-CR-JuanNeural']],
            'es-CU' => ['female' => ['es-CU-BelkysNeural'], 'male' => ['es-CU-ManuelNeural']],
            'es-DO' => ['female' => ['es-DO-RamonaNeural'], 'male' => ['es-DO-EmilioNeural']],
            'es-EC' => ['female' => ['es-EC-AndreaNeural'], 'male' => ['es-EC-LuisNeural']],
            'es-ES' => ['female' => ['es-ES-ElviraNeural', 'es-ES-ArabellaMultilingualNeural', 'es-ES-IsidoraMultilingualNeural', 'es-ES-XimenaMultilingualNeural', 'es-ES-AbrilNeural', 'es-ES-EstrellaNeural', 'es-ES-IreneNeural', 'es-ES-LaiaNeural', 'es-ES-LiaNeural', 'es-ES-TrianaNeural', 'es-ES-VeraNeural', 'es-ES-XimenaNeural', 'es-ES-Ximena:DragonHDLatestNeural'], 'male' => ['es-ES-AlvaroNeural', 'es-ES-TristanMultilingualNeural', 'es-ES-ArnauNeural', 'es-ES-DarioNeural', 'es-ES-EliasNeural', 'es-ES-NilNeural', 'es-ES-SaulNeural', 'es-ES-TeoNeural', 'es-ES-Tristan:DragonHDLatestNeural']],
            'es-GQ' => ['female' => ['es-GQ-TeresaNeural'], 'male' => ['es-GQ-JavierNeural']],
            'es-GT' => ['female' => ['es-GT-MartaNeural'], 'male' => ['es-GT-AndresNeural']],
            'es-HN' => ['female' => ['es-HN-KarlaNeural'], 'male' => ['es-HN-CarlosNeural']],
            'es-MX' => ['female' => ['es-MX-DaliaNeural', 'es-MX-DaliaMultilingualNeural', 'es-MX-BeatrizNeural', 'es-MX-CandelaNeural', 'es-MX-CarlotaNeural', 'es-MX-LarissaNeural', 'es-MX-MarinaNeural', 'es-MX-NuriaNeural', 'es-MX-RenataNeural'], 'male' => ['es-MX-JorgeNeural', 'es-MX-JorgeMultilingualNeural', 'es-MX-CecilioNeural', 'es-MX-GerardoNeural', 'es-MX-LibertoNeural', 'es-MX-LucianoNeural', 'es-MX-PelayoNeural', 'es-MX-YagoNeural']],
            'es-NI' => ['female' => ['es-NI-YolandaNeural'], 'male' => ['es-NI-FedericoNeural']],
            'es-PA' => ['female' => ['es-PA-MargaritaNeural'], 'male' => ['es-PA-RobertoNeural']],
            'es-PE' => ['female' => ['es-PE-CamilaNeural'], 'male' => ['es-PE-AlexNeural']],
            'es-PR' => ['female' => ['es-PR-KarinaNeural'], 'male' => ['es-PR-VictorNeural']],
            'es-PY' => ['female' => ['es-PY-TaniaNeural'], 'male' => ['es-PY-MarioNeural']],
            'es-SV' => ['female' => ['es-SV-LorenaNeural'], 'male' => ['es-SV-RodrigoNeural']],
            'es-US' => ['female' => ['es-US-PalomaNeural'], 'male' => ['es-US-AlonsoNeural']],
            'es-UY' => ['female' => ['es-UY-ValentinaNeural'], 'male' => ['es-UY-MateoNeural']],
            'es-VE' => ['female' => ['es-VE-PaolaNeural'], 'male' => ['es-VE-SebastianNeural']],
            'et-EE' => ['female' => ['et-EE-AnuNeural'], 'male' => ['et-EE-KertNeural']],
            'eu-ES' => ['female' => ['eu-ES-AinhoaNeural'], 'male' => ['eu-ES-AnderNeural']],
            'fa-IR' => ['female' => ['fa-IR-DilaraNeural'], 'male' => ['fa-IR-FaridNeural']],
            'fi-FI' => ['female' => ['fi-FI-SelmaNeural', 'fi-FI-NooraNeural'], 'male' => ['fi-FI-HarriNeural']],
            'fil-PH' => ['female' => ['fil-PH-BlessicaNeural'], 'male' => ['fil-PH-AngeloNeural']],
            'fr-BE' => ['female' => ['fr-BE-CharlineNeural'], 'male' => ['fr-BE-GerardNeural']],
            'fr-CA' => ['female' => ['fr-CA-SylvieNeural'], 'male' => ['fr-CA-JeanNeural', 'fr-CA-AntoineNeural', 'fr-CA-ThierryNeural']],
            'fr-CH' => ['female' => ['fr-CH-ArianeNeural'], 'male' => ['fr-CH-FabriceNeural']],
            'fr-FR' => ['female' => ['fr-FR-DeniseNeural', 'fr-FR-VivienneMultilingualNeural', 'fr-FR-BrigitteNeural', 'fr-FR-CelesteNeural', 'fr-FR-CoralieNeural', 'fr-FR-EloiseNeural', 'fr-FR-JacquelineNeural', 'fr-FR-JosephineNeural', 'fr-FR-YvetteNeural', 'fr-FR-Vivienne:DragonHDLatestNeural'], 'male' => ['fr-FR-HenriNeural', 'fr-FR-RemyMultilingualNeural', 'fr-FR-LucienMultilingualNeural', 'fr-FR-AlainNeural', 'fr-FR-ClaudeNeural', 'fr-FR-JeromeNeural', 'fr-FR-MauriceNeural', 'fr-FR-YvesNeural', 'fr-FR-Remy:DragonHDLatestNeural']],
            'ga-IE' => ['female' => ['ga-IE-OrlaNeural'], 'male' => ['ga-IE-ColmNeural']],
            'gl-ES' => ['female' => ['gl-ES-SabelaNeural'], 'male' => ['gl-ES-RoiNeural']],
            'gu-IN' => ['female' => ['gu-IN-DhwaniNeural'], 'male' => ['gu-IN-NiranjanNeural']],
            'he-IL' => ['female' => ['he-IL-HilaNeural'], 'male' => ['he-IL-AvriNeural']],
            'hi-IN' => ['female' => ['hi-IN-AnanyaNeural', 'hi-IN-AartiNeural', 'hi-IN-KavyaNeural', 'hi-IN-SwaraNeural'], 'male' => ['hi-IN-AaravNeural', 'hi-IN-ArjunNeural', 'hi-IN-KunalNeural', 'hi-IN-RehaanNeural', 'hi-IN-MadhurNeural']],
            'hr-HR' => ['female' => ['hr-HR-GabrijelaNeural'], 'male' => ['hr-HR-SreckoNeural']],
            'hu-HU' => ['female' => ['hu-HU-NoemiNeural'], 'male' => ['hu-HU-TamasNeural']],
            'hy-AM' => ['female' => ['hy-AM-AnahitNeural'], 'male' => ['hy-AM-HaykNeural']],
            'id-ID' => ['female' => ['id-ID-GadisNeural'], 'male' => ['id-ID-ArdiNeural']],
            'is-IS' => ['female' => ['is-IS-GudrunNeural'], 'male' => ['is-IS-GunnarNeural']],
            'it-IT' => ['female' => ['it-IT-ElsaNeural', 'it-IT-IsabellaNeural', 'it-IT-IsabellaMultilingualNeural', 'it-IT-FabiolaNeural', 'it-IT-FiammaNeural', 'it-IT-ImeldaNeural', 'it-IT-IrmaNeural', 'it-IT-PalmiraNeural', 'it-IT-PierinaNeural', 'it-IT-Isabella:DragonHDLatestNeural'], 'male' => ['it-IT-DiegoNeural', 'it-IT-AlessioMultilingualNeural', 'it-IT-GiuseppeMultilingualNeural', 'it-IT-MarcelloMultilingualNeural', 'it-IT-BenignoNeural', 'it-IT-CalimeroNeural', 'it-IT-CataldoNeural', 'it-IT-GianniNeural', 'it-IT-GiuseppeNeural', 'it-IT-LisandroNeural', 'it-IT-RinaldoNeural', 'it-IT-Alessio:DragonHDLatestNeural']],
            'iu-CANS-CA' => ['female' => ['iu-Cans-CA-SiqiniqNeural'], 'male' => ['iu-Cans-CA-TaqqiqNeural']],
            'iu-LATN-CA' => ['female' => ['iu-Latn-CA-SiqiniqNeural'], 'male' => ['iu-Latn-CA-TaqqiqNeural']],
            'ja-JP' => ['female' => ['ja-JP-NanamiNeural', 'ja-JP-AoiNeural', 'ja-JP-MayuNeural', 'ja-JP-ShioriNeural', 'ja-JP-Nanami:DragonHDLatestNeural'], 'male' => ['ja-JP-KeitaNeural', 'ja-JP-DaichiNeural', 'ja-JP-NaokiNeural', 'ja-JP-MasaruMultilingualNeural', 'ja-JP-Masaru:DragonHDLatestNeural']],
            'jv-ID' => ['female' => ['jv-ID-SitiNeural'], 'male' => ['jv-ID-DimasNeural']],
            'ka-GE' => ['female' => ['ka-GE-EkaNeural'], 'male' => ['ka-GE-GiorgiNeural']],
            'kk-KZ' => ['female' => ['kk-KZ-AigulNeural'], 'male' => ['kk-KZ-DauletNeural']],
            'km-KH' => ['female' => ['km-KH-SreymomNeural'], 'male' => ['km-KH-PisethNeural']],
            'kn-IN' => ['female' => ['kn-IN-SapnaNeural'], 'male' => ['kn-IN-GaganNeural']],
            'ko-KR' => ['female' => ['ko-KR-SunHiNeural', 'ko-KR-JiMinNeural', 'ko-KR-SeoHyeonNeural', 'ko-KR-SoonBokNeural', 'ko-KR-YuJinNeural'], 'male' => ['ko-KR-InJoonNeural', 'ko-KR-HyunsuMultilingualNeural', 'ko-KR-BongJinNeural', 'ko-KR-GookMinNeural', 'ko-KR-HyunsuNeural']],
            'lo-LA' => ['female' => ['lo-LA-KeomanyNeural'], 'male' => ['lo-LA-ChanthavongNeural']],
            'lt-LT' => ['female' => ['lt-LT-OnaNeural'], 'male' => ['lt-LT-LeonasNeural']],
            'lv-LV' => ['female' => ['lv-LV-EveritaNeural'], 'male' => ['lv-LV-NilsNeural']],
            'mk-MK' => ['female' => ['mk-MK-MarijaNeural'], 'male' => ['mk-MK-AleksandarNeural']],
            'ml-IN' => ['female' => ['ml-IN-SobhanaNeural'], 'male' => ['ml-IN-MidhunNeural']],
            'mn-MN' => ['female' => ['mn-MN-YesuiNeural'], 'male' => ['mn-MN-BataaNeural']],
            'mr-IN' => ['female' => ['mr-IN-AarohiNeural'], 'male' => ['mr-IN-ManoharNeural']],
            'ms-MY' => ['female' => ['ms-MY-YasminNeural'], 'male' => ['ms-MY-OsmanNeural']],
            'mt-MT' => ['female' => ['mt-MT-GraceNeural'], 'male' => ['mt-MT-JosephNeural']],
            'my-MM' => ['female' => ['my-MM-NilarNeural'], 'male' => ['my-MM-ThihaNeural']],
            'nb-NO' => ['female' => ['nb-NO-PernilleNeural', 'nb-NO-IselinNeural'], 'male' => ['nb-NO-FinnNeural']],
            'ne-NP' => ['female' => ['ne-NP-HemkalaNeural'], 'male' => ['ne-NP-SagarNeural']],
            'nl-BE' => ['female' => ['nl-BE-DenaNeural'], 'male' => ['nl-BE-ArnaudNeural']],
            'nl-NL' => ['female' => ['nl-NL-FennaNeural', 'nl-NL-ColetteNeural'], 'male' => ['nl-NL-MaartenNeural']],
            'or-IN' => ['female' => ['or-IN-SubhasiniNeural'], 'male' => ['or-IN-SukantNeural']],
            'pa-IN' => ['female' => ['pa-IN-VaaniNeural'], 'male' => ['pa-IN-OjasNeural']],
            'pl-PL' => ['female' => ['pl-PL-AgnieszkaNeural', 'pl-PL-ZofiaNeural'], 'male' => ['pl-PL-MarekNeural']],
            'ps-AF' => ['female' => ['ps-AF-LatifaNeural'], 'male' => ['ps-AF-GulNawazNeural']],
            'pt-BR' => ['female' => ['pt-BR-FranciscaNeural', 'pt-BR-ThalitaMultilingualNeural', 'pt-BR-BrendaNeural', 'pt-BR-ElzaNeural', 'pt-BR-GiovannaNeural', 'pt-BR-LeilaNeural', 'pt-BR-LeticiaNeural', 'pt-BR-ManuelaNeural', 'pt-BR-ThalitaNeural', 'pt-BR-YaraNeural', 'pt-BR-Thalita:DragonHDLatestNeural'], 'male' => ['pt-BR-AntonioNeural', 'pt-BR-MacerioMultilingualNeural', 'pt-BR-DonatoNeural', 'pt-BR-FabioNeural', 'pt-BR-HumbertoNeural', 'pt-BR-JulioNeural', 'pt-BR-NicolauNeural', 'pt-BR-ValerioNeural', 'pt-BR-Macerio:DragonHDLatestNeural']],
            'pt-PT' => ['female' => ['pt-PT-RaquelNeural', 'pt-PT-FernandaNeural'], 'male' => ['pt-PT-DuarteNeural']],
            'ro-RO' => ['female' => ['ro-RO-AlinaNeural'], 'male' => ['ro-RO-EmilNeural']],
            'ru-RU' => ['female' => ['ru-RU-SvetlanaNeural', 'ru-RU-DariyaNeural'], 'male' => ['ru-RU-DmitryNeural']],
            'si-LK' => ['female' => ['si-LK-ThiliniNeural'], 'male' => ['si-LK-SameeraNeural']],
            'sk-SK' => ['female' => ['sk-SK-ViktoriaNeural'], 'male' => ['sk-SK-LukasNeural']],
            'sl-SI' => ['female' => ['sl-SI-PetraNeural'], 'male' => ['sl-SI-RokNeural']],
            'so-SO' => ['female' => ['so-SO-UbaxNeural'], 'male' => ['so-SO-MuuseNeural']],
            'sq-AL' => ['female' => ['sq-AL-AnilaNeural'], 'male' => ['sq-AL-IlirNeural']],
            'sr-LATN-RS' => ['female' => ['sr-Latn-RS-SophieNeural'], 'male' => ['sr-Latn-RS-NicholasNeural']],
            'sr-RS' => ['female' => ['sr-RS-SophieNeural'], 'male' => ['sr-RS-NicholasNeural']],
            'su-ID' => ['female' => ['su-ID-TutiNeural'], 'male' => ['su-ID-JajangNeural']],
            'sv-SE' => ['female' => ['sv-SE-SofieNeural', 'sv-SE-HilleviNeural'], 'male' => ['sv-SE-MattiasNeural']],
            'sw-KE' => ['female' => ['sw-KE-ZuriNeural'], 'male' => ['sw-KE-RafikiNeural']],
            'sw-TZ' => ['female' => ['sw-TZ-RehemaNeural'], 'male' => ['sw-TZ-DaudiNeural']],
            'ta-IN' => ['female' => ['ta-IN-PallaviNeural'], 'male' => ['ta-IN-ValluvarNeural']],
            'ta-LK' => ['female' => ['ta-LK-SaranyaNeural'], 'male' => ['ta-LK-KumarNeural']],
            'ta-MY' => ['female' => ['ta-MY-KaniNeural'], 'male' => ['ta-MY-SuryaNeural']],
            'ta-SG' => ['female' => ['ta-SG-VenbaNeural'], 'male' => ['ta-SG-AnbuNeural']],
            'te-IN' => ['female' => ['te-IN-ShrutiNeural'], 'male' => ['te-IN-MohanNeural']],
            'th-TH' => ['female' => ['th-TH-PremwadeeNeural', 'th-TH-AcharaNeural'], 'male' => ['th-TH-NiwatNeural']],
            'tr-TR' => ['female' => ['tr-TR-EmelNeural'], 'male' => ['tr-TR-AhmetNeural']],
            'uk-UA' => ['female' => ['uk-UA-PolinaNeural'], 'male' => ['uk-UA-OstapNeural']],
            'ur-IN' => ['female' => ['ur-IN-GulNeural'], 'male' => ['ur-IN-SalmanNeural']],
            'ur-PK' => ['female' => ['ur-PK-UzmaNeural'], 'male' => ['ur-PK-AsadNeural']],
            'uz-UZ' => ['female' => ['uz-UZ-MadinaNeural'], 'male' => ['uz-UZ-SardorNeural']],
            'vi-VN' => ['female' => ['vi-VN-HoaiMyNeural'], 'male' => ['vi-VN-NamMinhNeural']],
            'wuu-CN' => ['female' => ['wuu-CN-XiaotongNeural'], 'male' => ['wuu-CN-YunzheNeural']],
            'yue-CN' => ['female' => ['yue-CN-XiaoMinNeural'], 'male' => ['yue-CN-YunSongNeural']],
            'zh-CN' => ['female' => ['zh-CN-XiaoxiaoNeural', 'zh-CN-XiaoyiNeural', 'zh-CN-XiaochenNeural', 'zh-CN-XiaochenMultilingualNeural', 'zh-CN-XiaohanNeural', 'zh-CN-XiaomengNeural', 'zh-CN-XiaomoNeural', 'zh-CN-XiaoqiuNeural', 'zh-CN-XiaorouNeural', 'zh-CN-XiaoruiNeural', 'zh-CN-XiaoshuangNeural', 'zh-CN-XiaoxiaoDialectsNeural', 'zh-CN-XiaoxiaoMultilingualNeural', 'zh-CN-XiaoyanNeural', 'zh-CN-XiaoyouNeural', 'zh-CN-XiaoyuMultilingualNeural', 'zh-CN-XiaozhenNeural', 'zh-CN-Xiaochen:DragonHDFlashLatestNeural', 'zh-CN-Xiaoxiao:DragonHDFlashLatestNeural', 'zh-CN-Xiaoxiao2:DragonHDFlashLatestNeural', 'zh-CN-Xiaochen:DragonHDLatestNeural'], 'male' => ['zh-CN-YunxiNeural', 'zh-CN-YunjianNeural', 'zh-CN-YunyangNeural', 'zh-CN-YunfengNeural', 'zh-CN-YunhaoNeural', 'zh-CN-YunjieNeural', 'zh-CN-YunxiaNeural', 'zh-CN-YunxiaoMultilingualNeural', 'zh-CN-YunyeNeural', 'zh-CN-YunyiMultilingualNeural', 'zh-CN-YunzeNeural', 'zh-CN-YunfanMultilingualNeural', 'zh-CN-Yunxiao:DragonHDFlashLatestNeural', 'zh-CN-Yunye:DragonHDFlashLatestNeural', 'zh-CN-Yunyi:DragonHDFlashLatestNeural', 'zh-CN-Yunfan:DragonHDLatestNeural']],
            'zh-CN-GUANGXI' => ['male' => ['zh-CN-guangxi-YunqiNeural']],
            'zh-CN-henan' => ['male' => ['zh-CN-henan-YundengNeural']],
            'zh-CN-liaoning' => ['female' => ['zh-CN-liaoning-XiaobeiNeural'], 'male' => ['zh-CN-liaoning-YunbiaoNeural']],
            'zh-CN-shaanxi' => ['female' => ['zh-CN-shaanxi-XiaoniNeural']],
            'zh-CN-shandong' => ['male' => ['zh-CN-shandong-YunxiangNeural']],
            'zh-CN-sichuan' => ['male' => ['zh-CN-sichuan-YunxiNeural']],
            'zh-HK' => ['female' => ['zh-HK-HiuMaanNeural', 'zh-HK-HiuGaaiNeural'], 'male' => ['zh-HK-WanLungNeural']],
            'zh-TW' => ['female' => ['zh-TW-HsiaoChenNeural', 'zh-TW-HsiaoYuNeural'], 'male' => ['zh-TW-YunJheNeural']],
            'zu-ZA' => ['female' => ['zu-ZA-ThandoNeural'], 'male' => ['zu-ZA-ThembaNeural']],*/
        ];

        foreach ($artists as $languageCode => $genders) {
            $ttsLanguages = TTSLanguage::where('code', $languageCode)->get();

            foreach ($ttsLanguages as $ttsLanguage) {
                foreach ($genders as $gender => $artistCodes) {
                    foreach ($artistCodes as $artistCode) {
                        $name = str_replace($languageCode.'-', '', $artistCode);
                        $name = str_replace([
                            'Multilingual', 'Neural', 'Turbo', ':DragonHDLatest', ':DragonHDFlashLatest',
                        ], '', $name);

                        TTSArtist::firstOrCreate([
                            'tts_language_id' => $ttsLanguage->id,
                            'code' => $artistCode,
                        ], [
                            'name' => $name,
                            'gender' => $gender,
                            'enabled' => true,
                        ]);
                    }
                }
            }
        }
    }
}
