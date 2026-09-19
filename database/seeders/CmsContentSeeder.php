<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Cms\Models\CmsContent;

class CmsContentSeeder extends Seeder
{
    public function run(): void
    {
        $panels = [
            [
                'module_key' => 'trade_union',
                'title' => ['en' => 'Trade Union', 'is' => 'Stéttarfélag'],
                'body' => [
                    'en' => "Here you can register which union you are, or have been, in and what rights may be associated with it. This gives your people a better overview of the payments, funds and services that the union offers.\n\nUnions can provide relatives with information and guidance, for example about grants, rights or the implementation of cases if needed. It is therefore useful to have this information available here.",
                    'is' => "Hér getur þú skráð í hvaða stéttarfélagi þú ert, eða hefur verið, og hvaða réttindi kunna að fylgja því. Þetta gefur aðstandendum þínum betri yfirsýn yfir greiðslur, sjóði og þjónustu sem félagið býður upp á.\n\nStéttarfélög geta veitt aðstandendum upplýsingar og leiðbeiningar, til dæmis um styrki, réttindi eða framkvæmd mála ef þörf krefur.",
                ],
            ],
            [
                'module_key' => 'commercial_bank',
                'title' => ['en' => 'Commercial Bank', 'is' => 'Viðskiptabanki'],
                'body' => [
                    'en' => "Here you can record basic information about finances, assets, and liabilities.\n\nBy keeping these items in one place, you enable family members to access information in a clear and simple way when needed.",
                    'is' => "Hér getur þú skráð grunnupplýsingar um fjármál, eignir og skuldir.\n\nMeð því að hafa þessi atriði á einum stað gerir þú aðstandendum kleift að nálgast upplýsingar á skýran og einfaldan hátt þegar á þarf að halda.",
                ],
            ],
            [
                'module_key' => 'pension_private',
                'title' => ['en' => 'Private Savings', 'is' => 'Séreignasparnaður'],
                'body' => [
                    'en' => "Here you can summarize your private equity savings and list all funds related to it, even more than one.\n\nIf you are unsure which private pension funds you have paid into over the years, here is a template for an email that you can send to the funds.",
                    'is' => "Hér getur þú tekið saman séreignarsparnaðinn þinn og skráð alla sjóði sem tengjast honum, jafnvel fleiri en einn.\n\nEf þú ert óviss um hvaða séreignarsjóði þú hefur greitt í gegnum árin, er hér skapalón fyrir tölvupóst sem þú getur sent á sjóðina.",
                ],
            ],
            [
                'module_key' => 'pension_obligatory',
                'title' => ['en' => 'Obligatory Savings', 'is' => 'Lögbundinn lífeyrissparnaður'],
                'body' => [
                    'en' => "Here you can compile information about your mandatory savings and list the pension funds you have paid into throughout your working life.\n\nBy recording this information, you make it easier for family members to find entitlements, send notifications, and complete pension settlement in a safer and simpler way when needed.",
                    'is' => "Hér getur þú tekið saman upplýsingar um lögbundinn lífeyrissparnað þinn og skráð þá lífeyrissjóði sem þú hefur greitt í á starfsævinni.\n\nMeð því að skrá þessar upplýsingar auðveldar þú aðstandendum að finna réttindi, senda tilkynningar og ljúka uppgjöri lífeyris á öruggari og einfaldari hátt.",
                ],
            ],
            [
                'module_key' => 'insurance',
                'title' => ['en' => 'Insurance', 'is' => 'Tryggingar'],
                'body' => [
                    'en' => "Here you can register your insurance in one place, so that family members have a clear overview if they need to access information. This can apply to home insurance, vehicle insurance, health and life insurance or other insurance that is relevant.",
                    'is' => "Hér getur þú skráð tryggingarnar þínar á einum stað svo aðstandendur hafi skýra yfirsýn ef þeir þurfa að nálgast upplýsingar. Þetta getur átt við um heimilistryggingu, ökutækjatryggingu, sjúkra- og líftryggingu eða aðrar tryggingar sem við eiga.",
                ],
            ],
            [
                'module_key' => 'funeral',
                'title' => ['en' => 'My Funeral', 'is' => 'Útförin mín'],
                'body' => [
                    'en' => "Here you can list your wishes regarding the funeral, ceremony, and other things you want your family to consider.\n\nThe goal is to make it easier for them to follow your wishes and support them in the decisions that need to be made.",
                    'is' => "Hér getur þú skráð óskir þínar varðandi útförina, athöfnina og annað sem þú vilt að fjölskyldan þín hafi í huga.\n\nMarkmiðið er að auðvelda þeim að fylgja óskum þínum og styðja þau í þeim ákvörðunum sem taka þarf.",
                ],
            ],
            [
                'module_key' => 'death_notice',
                'title' => ['en' => 'Death Notice', 'is' => 'Dánartilkynning'],
                'body' => [
                    'en' => "Here you can record information that will make it easier for relatives to report and prepare a death notice for the media.",
                    'is' => "Hér getur þú skráð upplýsingar sem auðvelda aðstandendum að tilkynna og undirbúa dánartilkynningu fyrir fjölmiðla.",
                ],
            ],
            [
                'module_key' => 'relatives',
                'title' => ['en' => 'Relatives', 'is' => 'Aðstandendur'],
                'body' => [
                    'en' => "Register the family members who should eventually have access to the information you record in FYRIRHYGGJA, and what they are allowed to see.",
                    'is' => "Skráðu þá aðstandendur sem eiga síðar meir að fá aðgang að upplýsingunum sem þú skráir í FYRIRHYGGJA, og hvað þeir mega sjá.",
                ],
            ],
            [
                'module_key' => 'general',
                'title' => ['en' => 'About FYRIRHYGGJA', 'is' => 'Um FYRIRHYGGJU'],
                'body' => [
                    'en' => "FYRIRHYGGJA helps you organize your pension, insurance, banking and end-of-life wishes in one secure place, so your family isn't left guessing.",
                    'is' => "FYRIRHYGGJA hjálpar þér að skipuleggja lífeyri, tryggingar, bankamál og óskir varðandi lífslok á einum öruggum stað, svo fjölskyldan þín þurfi ekki að giska.",
                ],
            ],
        ];

        foreach ($panels as $index => $panel) {
            CmsContent::query()->updateOrCreate(
                ['type' => 'help_panel', 'module_key' => $panel['module_key']],
                ['title' => $panel['title'], 'body' => $panel['body'], 'is_active' => true, 'sort_order' => $index]
            );
        }

        $faqs = [
            [
                'title' => ['en' => 'Why is it useful to register my trade union here?', 'is' => 'Af hverju er gagnlegt að skrá stéttarfélagið mitt hér?'],
                'body' => [
                    'en' => 'It gives your relatives a clear starting point for contacting the right union after your day, without having to search for it themselves.',
                    'is' => 'Það gefur aðstandendum þínum skýran útgangspunkt til að hafa samband við rétt stéttarfélag eftir þinn dag, án þess að þurfa að leita að því sjálf.',
                ],
                'module_key' => 'trade_union',
            ],
            [
                'title' => ['en' => 'What can trade unions help with?', 'is' => 'Hvað geta stéttarfélög aðstoðað við?'],
                'body' => [
                    'en' => 'Depending on the union, relatives may be entitled to a funeral grant, death benefit, or accident benefit, and can get guidance on next steps.',
                    'is' => 'Eftir því hvaða félag á í hlut geta aðstandendur átt rétt á útfarastyrk, dánarbótum eða slysabótum, og fengið leiðbeiningar um næstu skref.',
                ],
                'module_key' => 'trade_union',
            ],
            [
                'title' => ['en' => 'Am I providing sensitive information?', 'is' => 'Er ég að gefa upp viðkvæmar upplýsingar?'],
                'body' => [
                    'en' => 'You choose how much detail to include. Only what you save is stored, and it is only shared with the relatives you grant access to.',
                    'is' => 'Þú ræður hversu ítarlegar upplýsingarnar eru. Aðeins það sem þú vistar er geymt, og því er eingöngu deilt með þeim aðstandendum sem þú veitir aðgang.',
                ],
                'module_key' => 'insurance',
            ],
        ];

        foreach ($faqs as $index => $faq) {
            CmsContent::query()->updateOrCreate(
                ['type' => 'faq', 'module_key' => $faq['module_key'], 'title->en' => $faq['title']['en']],
                ['title' => $faq['title'], 'body' => $faq['body'], 'is_active' => true, 'sort_order' => $index]
            );
        }
    }
}
