<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Paragraph;

class ParagraphSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Paragraph::insert([
            [
                "content" => '<p style="margin-left: 36pt; margin-right: 0cm; text-align: center;"><span style="font-size: 11pt;"><span style="background-color: white;"><span style="font-family: Arial,sans-serif;"><strong>1. Человек. Природное и общественное в человеке</strong></span></span></span></p>
                <p style="margin-left: 0cm; margin-right: 0cm;"> </p>
                <p style="margin-left: 0cm; margin-right: 0cm;"><span style="font-size: 11pt;"><span style="background-color: white;"><span style="font-family: Arial,sans-serif;"><strong>Человек </strong>&mdash; Биосоциальное существо, обладающее членораздельной речью, сознанием, высшими психическими функциями, способное создавать орудия и пользоваться ими в процессе общественного труда.</span></span></span></p>
                <p style="margin-left: 0cm; margin-right: 0cm;"><span style="font-size: 11pt;"><span style="background-color: white;"><span style="font-family: Arial,sans-serif;"> </span></span></span></p>',
                "sort" => 1,
                "theme" => 1
            ],
            [
                "content" => '<blockquote>
                <p style="margin-left: 0cm; margin-right: 0cm;"><span style="color: #ced4d9;"><strong><span style="font-size: 11pt;"><span style="background-color: white;"><span style="font-family: Arial,sans-serif;">Дополнительная информация</span></span></span></strong></span></p>
                <p style="margin-left: 0cm; margin-right: 0cm;"><span style="font-family: arial, helvetica, sans-serif;"><strong style="font-size: 11pt;"><span style="color: #cccccc;">Антропология </span></strong><span style="color: #cccccc;">&mdash; совокупность научных дисциплин, занимающихся изучением человека, его происхождения, развития, существования в природной<span style="margin-right: 0.3em"> </span> <span style="margin-left: -0.3em">(естественной</span>) и культурной<span style="margin-right: 0.3em"> </span> <span style="margin-left: -0.3em">(искусственной</span>) средах. </span></span></p>
                <p style="margin-left: 0cm; margin-right: 0cm;"><span style="font-family: arial, helvetica, sans-serif;"><strong style="font-size: 11pt;"><span style="color: #cccccc;"><span style="font-size: 11pt;"><span style="background-color: white;"><span style="font-family: Arial,sans-serif;"> </span></span></span></span></strong></span></p>
                <p style="margin-left: 0cm; margin-right: 0cm;"><span style="font-family: arial, helvetica, sans-serif;"><strong><span style="font-size: 11pt;"><span style="color: #cccccc;">Антропогенез </span></span></strong><span style="color: #cccccc;">&mdash; процесс становления и развития человека.</span></span></p>
                <p style="margin-left: 0cm; margin-right: 0cm;"><span style="font-size: 11pt;"><span style="background-color: white;"><span style="font-family: Arial,sans-serif;"> </span></span></span></p>
                <p style="margin-left: 0cm; margin-right: 0cm;"><span style="font-family: arial, helvetica, sans-serif;"><strong style="font-size: 11pt;"><span style="color: #cccccc;">Теории происхождения человека</span></strong></span></p>
                <p style="margin-left: 0cm; margin-right: 0cm;"><span style="font-family: arial, helvetica, sans-serif;"><em style="font-size: 11pt;"><span style="color: #cccccc;">Религиозная теория: </span></em><span style="color: #cccccc;">Божественное происхождение. </span></span></p>
                <p style="margin-left: 0cm; margin-right: 0cm;"><span style="font-family: arial, helvetica, sans-serif;"><em style="font-size: 11pt;"><span style="color: #cccccc;">Естественно-научные<span style="margin-right: 0.3em"> </span> <span style="margin-left: -0.3em">(материалистические</span>), биосоциальные теории:</span></em></span></p>
                <p style="margin-left: 0cm; margin-right: 0cm;"><span style="color: #cccccc; font-family: arial, helvetica, sans-serif; font-size: 11pt;">а) Ч. Дарвин<span style="margin-right: 0.3em"> </span> <span style="margin-left: -0.3em">(естественного</span> отбора, борьбы за существование);</span></p>
                <p style="margin-left: 0cm; margin-right: 0cm;"><span style="color: #cccccc; font-family: arial, helvetica, sans-serif; font-size: 11pt;">б) Ф. Энгельса<span style="margin-right: 0.3em"> </span> <span style="margin-left: -0.3em">«Роль</span> труда»</span></p>
                </blockquote>',
                "sort" => 2,
                "theme" => 1
            ],
            [
                "content" => '<p style="margin-left: 0cm; margin-right: 0cm;"> </p>
                <p style="margin-left: 0cm; margin-right: 0cm;"><span style="font-size: 11pt;"><span style="background-color: white;"><span style="font-family: Arial,sans-serif;"><strong>Человек как биосоциальное<span style="margin-right: 0.3em"> </span> <span style="margin-left: -0.3em">(биопсихосоциальное</span>) существо</strong></span></span></span></p>
                <p style="margin-left: 0cm; margin-right: 0cm;"><span style="font-size: 11pt;"><span style="background-color: white;"><span style="font-family: Arial,sans-serif;">Сложно организованное единство биологического и социального: является частью природы; а развивается под влиянием и природы, и общества.</span></span></span></p>
                <p style="margin-left: 0cm; margin-right: 0cm;"><span style="font-size: 11pt;"><span style="font-family: Arial,sans-serif;"><em>Биологическое существо</em> &mdash; вид Homo sapiens. Биологическая природа проявляется в анатомии, физиологии. &bull; Не все свойства человека биологически жёстко запрограммированы. Следовательно большую роль играет социальное начало. &rArr; Биологическое начало &mdash; <u>предпосылка существования человека</u>.</span></span></p>
                <p style="margin-left: 0cm; margin-right: 0cm;"><span style="font-size: 11pt;"><span style="font-family: Arial,sans-serif;"><em>Социальное существо</em> &mdash; Связан с обществом. Становится человеком, лишь через общественные отношения. &rArr; <u>Сущность человека</u>. Личность связана с социальной природой человека.</span></span></p>
                <p style="margin-left: 0cm; margin-right: 0cm;"> </p>',
                "sort" => 4,
                "theme" => 1
            ],
            [
                "content" => '<p style="margin-left: 0cm; margin-right: 0cm;"><span style="font-size: 11pt;"><span style="font-family: Arial,sans-serif;"><span style="color: #38761d;">*<em><u>Маугли не сможет стать полноценным членом общества</u></em></span>.</span></span></p>
                <p style="margin-left: 0cm; margin-right: 0cm;"> </p>
                <p style="margin-left: 0cm; margin-right: 0cm;"><span style="font-size: 11pt;"><span style="font-family: Arial,sans-serif;"><strong><span style="color: red;">Проявления человеческой природы</span></strong></span></span></p>
                <p style="margin-left: 0cm; margin-right: 0cm;"><span style="font-size: 11pt;"><span style="font-family: Arial,sans-serif;"><strong><em>Биологическая природа: </em></strong>раса, пол, возраст, особенности телосложения, генотип, наследственность, <span style="color: #ff3030;">генетическая предрасположенность</span>, кровеносная, мышечная, нервная и другие системы, инстинктивное поведение, физиологические потребности, физическое развитие; <em>Психика </em><span style="margin-right: 0.3em"> </span> <span style="margin-left: -0.3em">(</span>*зачастую относится к биологической): эмоции, чувства, воля, память, быстрота реакций, моторика, <span style="color: red;">темперамент, характер, особенности развития психики</span><span style="color: #38761d;">,</span> <span style="color: #38761d;">сообразительность<span style="margin-right: 0.3em"> </span> <span style="margin-left: -0.3em">(</span>?не социальное &mdash; способность к познавательной деятельности)</span>.</span></span></p>
                <p style="margin-left: 0cm; margin-right: 0cm;"><span style="font-size: 11pt;"><span style="font-family: Arial,sans-serif;"><strong><em>Социальная природа:</em> </strong><strong> </strong>Дух. потребности: ценности, идеалы, умения, знания, <span style="color: #ff3030;">любознательность,</span> начитанность, свобода и ответственность, моральные качества<span style="margin-right: 0.3em"> </span> <span style="margin-left: -0.3em">(оценка</span> с позиции хорошо/плохо: порядочность, самоотверженность, <span style="color: #38761d;">доброта</span>, отзывчивость, послушание, прилежание), мировоззрение, религия, искусство, стремление к самореализации, <span style="color: red;">аккуратность</span>, законопослушность, способность оценивать, видеть цель; Соц потребности: общение, привязанность, дружба; Деятельность: способность и готовность к полезному труду, совместной преобразовательной деятельности, трудолюбие, художественная, творческая, способность преобразовывать мир, <span style="color: red;">мышление<span style="margin-right: 0.3em"> </span> <span style="margin-left: -0.3em">(сознание</span>) и разум</span>; созидание; целеполагание и др.</span></span></p>
                <p style="margin-left: 0cm; margin-right: 0cm;"> </p>',
                "sort" => 5,
                "theme" => 1
            ],
            [
                "content" => '<p style="margin-left: 0cm; margin-right: 0cm;"><span style="font-size: 11pt;"><span style="font-family: Arial,sans-serif;">Природная предрасположенность к каким-либо видам деятельности проявляется в социальных обстоятельствах.</span></span></p>
                <p style="margin-left: 0cm; margin-right: 0cm;"><span style="font-size: 11pt;"><span style="font-family: Arial,sans-serif;"><strong><em>Единство биологического и соц.:</em></strong><em> </em></span></span></p>
                <p style="margin-left: 0cm; margin-right: 0cm;"><span style="font-size: 11pt;"><span style="font-family: Arial,sans-serif;">&bull; Природная предрасположенность к каким-либо видам деятельности проявляется в социальных обстоятельствах; </span></span></p>
                <p style="margin-left: 0cm; margin-right: 0cm;"><span style="font-size: 11pt;"><span style="font-family: Arial,sans-serif;">&bull; Биологические потребности при удовлетворении становятся социальными; </span></span></p>
                <p style="margin-left: 0cm; margin-right: 0cm;"><span style="font-size: 11pt;"><span style="font-family: Arial,sans-serif;">&bull; Роль наследственности в развитии человека; </span></span></p>
                <p style="margin-left: 0cm; margin-right: 0cm;"><span style="font-size: 11pt;"><span style="font-family: Arial,sans-serif;">&bull; Современные технологии устраняют борются с наследственными заболеваниями.</span></span></p>
                <p style="margin-left: 0cm; margin-right: 0cm;"> </p>',
                "sort" => 6,
                "theme" => 1
            ],
            [
                "content" => '<p>sdf</p>',
                "sort" => 7,
                "theme" => 1
            ],
        ]);
    }
}
