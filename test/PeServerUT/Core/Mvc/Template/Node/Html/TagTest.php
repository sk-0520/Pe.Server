<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node\Html;

use PeServerTest\TestClass;
use PeServer\Core\Mvc\Template\Node\Html\Tag;
use PeServer\Core\Throws\ArgumentException;
use PHPUnit\Framework\Attributes\TestWith;

class TagTest extends TestClass
{
	#region function

	public function test_html()
	{
		$tag = new Tag();
		$actual = $tag->html();
		$this->assertSame('<html></html>', (string)$actual);
	}

	public function test_head()
	{
		$tag = new Tag();
		$actual = $tag->head();
		$this->assertSame('<head></head>', (string)$actual);
	}

	public function test_title()
	{
		$tag = new Tag();
		$actual = $tag->title();
		$this->assertSame('<title></title>', (string)$actual);
	}

	public function test_base()
	{
		$tag = new Tag();
		$actual = $tag->base();
		$this->assertSame('<base />', (string)$actual);
	}

	public function test_link()
	{
		$tag = new Tag();
		$actual = $tag->link();
		$this->assertSame('<link />', (string)$actual);
	}

	public function test_style()
	{
		$tag = new Tag();
		$actual = $tag->style();
		$this->assertSame('<style></style>', (string)$actual);
	}

	public function test_script()
	{
		$tag = new Tag();
		$actual = $tag->script();
		$this->assertSame('<script></script>', (string)$actual);
	}

	public function test_meta()
	{
		$tag = new Tag();
		$actual = $tag->meta();
		$this->assertSame('<meta />', (string)$actual);
	}

	public function test_noscript()
	{
		$tag = new Tag();
		$actual = $tag->noscript();
		$this->assertSame('<noscript></noscript>', (string)$actual);
	}

	public function test_template()
	{
		$tag = new Tag();
		$actual = $tag->template();
		$this->assertSame('<template></template>', (string)$actual);
	}

	public function test_body()
	{
		$tag = new Tag();
		$actual = $tag->body();
		$this->assertSame('<body></body>', (string)$actual);
	}

	public function test_a()
	{
		$tag = new Tag();
		$actual = $tag->a();
		$this->assertSame('<a></a>', (string)$actual);
	}

	public function test_abbr()
	{
		$tag = new Tag();
		$actual = $tag->abbr();
		$this->assertSame('<abbr></abbr>', (string)$actual);
	}

	public function test_address()
	{
		$tag = new Tag();
		$actual = $tag->address();
		$this->assertSame('<address></address>', (string)$actual);
	}

	public function test_area()
	{
		$tag = new Tag();
		$actual = $tag->area();
		$this->assertSame('<area />', (string)$actual);
	}

	public function test_article()
	{
		$tag = new Tag();
		$actual = $tag->article();
		$this->assertSame('<article></article>', (string)$actual);
	}

	public function test_aside()
	{
		$tag = new Tag();
		$actual = $tag->aside();
		$this->assertSame('<aside></aside>', (string)$actual);
	}

	public function test_audio()
	{
		$tag = new Tag();
		$actual = $tag->audio();
		$this->assertSame('<audio></audio>', (string)$actual);
	}

	public function test_b()
	{
		$tag = new Tag();
		$actual = $tag->b();
		$this->assertSame('<b></b>', (string)$actual);
	}

	public function test_bdi()
	{
		$tag = new Tag();
		$actual = $tag->bdi();
		$this->assertSame('<bdi></bdi>', (string)$actual);
	}

	public function test_bdo()
	{
		$tag = new Tag();
		$actual = $tag->bdo();
		$this->assertSame('<bdo></bdo>', (string)$actual);
	}

	public function test_br()
	{
		$tag = new Tag();
		$actual = $tag->br();
		$this->assertSame('<br />', (string)$actual);
	}

	public function test_blockquote()
	{
		$tag = new Tag();
		$actual = $tag->blockquote();
		$this->assertSame('<blockquote></blockquote>', (string)$actual);
	}

	public function test_button()
	{
		$tag = new Tag();
		$actual = $tag->button();
		$this->assertSame('<button></button>', (string)$actual);
	}

	public function test_canvas()
	{
		$tag = new Tag();
		$actual = $tag->canvas();
		$this->assertSame('<canvas></canvas>', (string)$actual);
	}

	public function test_caption()
	{
		$tag = new Tag();
		$actual = $tag->caption();
		$this->assertSame('<caption></caption>', (string)$actual);
	}

	public function test_cite()
	{
		$tag = new Tag();
		$actual = $tag->cite();
		$this->assertSame('<cite></cite>', (string)$actual);
	}

	public function test_code()
	{
		$tag = new Tag();
		$actual = $tag->code();
		$this->assertSame('<code></code>', (string)$actual);
	}

	public function test_col()
	{
		$tag = new Tag();
		$actual = $tag->col();
		$this->assertSame('<col />', (string)$actual);
	}

	public function test_colgroup()
	{
		$tag = new Tag();
		$actual = $tag->colgroup();
		$this->assertSame('<colgroup></colgroup>', (string)$actual);
	}

	public function test_data()
	{
		$tag = new Tag();
		$actual = $tag->data();
		$this->assertSame('<data></data>', (string)$actual);
	}

	public function test_datalist()
	{
		$tag = new Tag();
		$actual = $tag->datalist();
		$this->assertSame('<datalist></datalist>', (string)$actual);
	}

	public function test_dd()
	{
		$tag = new Tag();
		$actual = $tag->dd();
		$this->assertSame('<dd></dd>', (string)$actual);
	}

	public function test_del()
	{
		$tag = new Tag();
		$actual = $tag->del();
		$this->assertSame('<del></del>', (string)$actual);
	}

	public function test_details()
	{
		$tag = new Tag();
		$actual = $tag->details();
		$this->assertSame('<details></details>', (string)$actual);
	}

	public function test_dfn()
	{
		$tag = new Tag();
		$actual = $tag->dfn();
		$this->assertSame('<dfn></dfn>', (string)$actual);
	}

	public function test_div()
	{
		$tag = new Tag();
		$actual = $tag->div();
		$this->assertSame('<div></div>', (string)$actual);
	}

	public function test_dialog()
	{
		$tag = new Tag();
		$actual = $tag->dialog();
		$this->assertSame('<dialog></dialog>', (string)$actual);
	}

	public function test_dl()
	{
		$tag = new Tag();
		$actual = $tag->dl();
		$this->assertSame('<dl></dl>', (string)$actual);
	}

	public function test_dt()
	{
		$tag = new Tag();
		$actual = $tag->dt();
		$this->assertSame('<dt></dt>', (string)$actual);
	}

	public function test_em()
	{
		$tag = new Tag();
		$actual = $tag->em();
		$this->assertSame('<em></em>', (string)$actual);
	}

	public function test_embed()
	{
		$tag = new Tag();
		$actual = $tag->embed();
		$this->assertSame('<embed />', (string)$actual);
	}

	public function test_fieldset()
	{
		$tag = new Tag();
		$actual = $tag->fieldset();
		$this->assertSame('<fieldset></fieldset>', (string)$actual);
	}

	public function test_figcaption()
	{
		$tag = new Tag();
		$actual = $tag->figcaption();
		$this->assertSame('<figcaption></figcaption>', (string)$actual);
	}

	public function test_figure()
	{
		$tag = new Tag();
		$actual = $tag->figure();
		$this->assertSame('<figure></figure>', (string)$actual);
	}

	public function test_footer()
	{
		$tag = new Tag();
		$actual = $tag->footer();
		$this->assertSame('<footer></footer>', (string)$actual);
	}

	public function test_form()
	{
		$tag = new Tag();
		$actual = $tag->form();
		$this->assertSame('<form></form>', (string)$actual);
	}

	#[TestWith(['h1', 1])]
	#[TestWith(['h2', 2])]
	#[TestWith(['h3', 3])]
	#[TestWith(['h4', 4])]
	#[TestWith(['h5', 5])]
	#[TestWith(['h6', 6])]
	public function test_h(string $expected, int $level)
	{
		$tag = new Tag();
		$actual = $tag->h($level);
		$this->assertSame("<{$expected}></{$expected}>", (string)$actual);
	}

	#[TestWith([0])]
	#[TestWith([7])]
	public function test_h_throw(int $level)
	{
		$tag = new Tag();
		$this->expectException(ArgumentException::class);
		$tag->h($level);
		$this->fail();
	}

	public function test_h1()
	{
		$tag = new Tag();
		$actual = $tag->h1();
		$this->assertSame("<h1></h1>", (string)$actual);
	}

	public function test_h2()
	{
		$tag = new Tag();
		$actual = $tag->h2();
		$this->assertSame("<h2></h2>", (string)$actual);
	}

	public function test_h3()
	{
		$tag = new Tag();
		$actual = $tag->h3();
		$this->assertSame("<h3></h3>", (string)$actual);
	}

	public function test_h4()
	{
		$tag = new Tag();
		$actual = $tag->h4();
		$this->assertSame("<h4></h4>", (string)$actual);
	}

	public function test_h5()
	{
		$tag = new Tag();
		$actual = $tag->h5();
		$this->assertSame("<h5></h5>", (string)$actual);
	}

	public function test_6()
	{
		$tag = new Tag();
		$actual = $tag->h6();
		$this->assertSame("<h6></h6>", (string)$actual);
	}

	public function test_header()
	{
		$tag = new Tag();
		$actual = $tag->header();
		$this->assertSame("<header></header>", (string)$actual);
	}

	public function test_hgroup()
	{
		$tag = new Tag();
		$actual = $tag->hgroup();
		$this->assertSame("<hgroup></hgroup>", (string)$actual);
	}

	public function test_hr()
	{
		$tag = new Tag();
		$actual = $tag->hr();
		$this->assertSame("<hr />", (string)$actual);
	}

	public function test_i()
	{
		$tag = new Tag();
		$actual = $tag->i();
		$this->assertSame("<i></i>", (string)$actual);
	}

	public function test_iframe()
	{
		$tag = new Tag();
		$actual = $tag->iframe();
		$this->assertSame("<iframe></iframe>", (string)$actual);
	}

	public function test_img()
	{
		$tag = new Tag();
		$actual = $tag->img();
		$this->assertSame("<img />", (string)$actual);
	}

	public function test_input()
	{
		$tag = new Tag();
		$actual = $tag->input();
		$this->assertSame("<input />", (string)$actual);
	}

	public function test_ins()
	{
		$tag = new Tag();
		$actual = $tag->ins();
		$this->assertSame("<ins></ins>", (string)$actual);
	}

	public function test_kbd()
	{
		$tag = new Tag();
		$actual = $tag->kbd();
		$this->assertSame("<kbd></kbd>", (string)$actual);
	}

	public function test_label()
	{
		$tag = new Tag();
		$actual = $tag->label();
		$this->assertSame("<label></label>", (string)$actual);
	}

	public function test_legend()
	{
		$tag = new Tag();
		$actual = $tag->legend();
		$this->assertSame("<legend></legend>", (string)$actual);
	}

	public function test_li()
	{
		$tag = new Tag();
		$actual = $tag->li();
		$this->assertSame("<li></li>", (string)$actual);
	}

	public function test_main()
	{
		$tag = new Tag();
		$actual = $tag->main();
		$this->assertSame("<main></main>", (string)$actual);
	}

	public function test_map()
	{
		$tag = new Tag();
		$actual = $tag->map();
		$this->assertSame("<map></map>", (string)$actual);
	}

	public function test_mark()
	{
		$tag = new Tag();
		$actual = $tag->mark();
		$this->assertSame("<mark></mark>", (string)$actual);
	}

	public function test_menu()
	{
		$tag = new Tag();
		$actual = $tag->menu();
		$this->assertSame("<menu></menu>", (string)$actual);
	}

	public function test_meter()
	{
		$tag = new Tag();
		$actual = $tag->meter();
		$this->assertSame("<meter></meter>", (string)$actual);
	}

	public function test_nav()
	{
		$tag = new Tag();
		$actual = $tag->nav();
		$this->assertSame("<nav></nav>", (string)$actual);
	}

	public function test_object()
	{
		$tag = new Tag();
		$actual = $tag->object();
		$this->assertSame("<object></object>", (string)$actual);
	}

	public function test_ol()
	{
		$tag = new Tag();
		$actual = $tag->ol();
		$this->assertSame("<ol></ol>", (string)$actual);
	}

	public function test_optgroup()
	{
		$tag = new Tag();
		$actual = $tag->optgroup();
		$this->assertSame("<optgroup></optgroup>", (string)$actual);
	}

	public function test_option()
	{
		$tag = new Tag();
		$actual = $tag->option();
		$this->assertSame("<option></option>", (string)$actual);
	}

	public function test_output()
	{
		$tag = new Tag();
		$actual = $tag->output();
		$this->assertSame("<output></output>", (string)$actual);
	}

	public function test_p()
	{
		$tag = new Tag();
		$actual = $tag->p();
		$this->assertSame("<p></p>", (string)$actual);
	}

	public function test_picture()
	{
		$tag = new Tag();
		$actual = $tag->picture();
		$this->assertSame("<picture></picture>", (string)$actual);
	}

	public function test_pre()
	{
		$tag = new Tag();
		$actual = $tag->pre();
		$this->assertSame("<pre></pre>", (string)$actual);
	}

	public function test_progress()
	{
		$tag = new Tag();
		$actual = $tag->progress();
		$this->assertSame("<progress></progress>", (string)$actual);
	}

	public function test_q()
	{
		$tag = new Tag();
		$actual = $tag->q();
		$this->assertSame("<q></q>", (string)$actual);
	}

	public function test_rp()
	{
		$tag = new Tag();
		$actual = $tag->rp();
		$this->assertSame("<rp></rp>", (string)$actual);
	}

	public function test_rt()
	{
		$tag = new Tag();
		$actual = $tag->rt();
		$this->assertSame("<rt></rt>", (string)$actual);
	}

	public function test_ruby()
	{
		$tag = new Tag();
		$actual = $tag->ruby();
		$this->assertSame("<ruby></ruby>", (string)$actual);
	}









	#endregion
}
