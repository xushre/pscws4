<?php


namespace Tests;

use PHPUnit\Framework\TestCase;
use SCWS\PSCWS4;

require __DIR__ . '/../vendor/autoload.php';


class Test extends TestCase
{

    public function testBasicTest()
    {
        //名字允许复查?
        $text = <<<EOF
中国航天官员应邀到美国与太空总署官员开会
发展中国家
上海大学城书店
表面的东西
今天我买了一辆面的，于是我坐着面的去上班
化妆和服装
这个门把手坏了，请把手拿开
将军任命了一名中将，产量三年中将增长两倍
王军虎去广州了，王军虎头虎脑的
欧阳明练功很厉害可是马明练不厉害
毛泽东北京华烟云
人中出吕布 马中出赤兔Q1,中我要买Q币充值
EOF;

        $cws = new PSCWS4('utf8');
        $cws->set_dict(__DIR__ . '/../src/dict/dict.utf8.xdb');
        $cws->set_rule(__DIR__ . '/../src/etc/rules.ini');
        //$cws->set_multi(3);
        $cws->set_ignore(true);
        //$cws->set_debug(true);
        //$cws->set_duality(true);
        $cws->send_text($text);

        if (php_sapi_name() != 'cli') header('Content-Type: text/plain');
        echo "pscws version: " . $cws->version() . "\n";
        echo "Segment result:\n\n";
        while ($tmp = $cws->get_result()) {
            $line = '';
            foreach ($tmp as $w) {
                if ($w['word'] == "\r") continue;
                if ($w['word'] == "\n")
                    $line = rtrim($line, ' ') . "\n";
                //else $line .= $w['word'] . "/{$w['attr']} ";
                else $line .= $w['word'] . " ";
            }
            echo $line;
        }

// top:
        echo PHP_EOL;
        echo PHP_EOL;
        echo "Top words stats:\n\n";
        $ret = $cws->get_tops(10, 'r,v,p');
        echo "No.\tWord\t\t\tAttr\tTimes\tRank\n------------------------------------------------------\n";
        $i = 1;
        foreach ($ret as $tmp) {
            printf("%02d.\t%-16s\t%s\t%d\t%.2f\n", $i++, $tmp['word'], $tmp['attr'], $tmp['times'], $tmp['weight']);
        }
        $cws->close();

        $this->assertTrue(!empty($ret));
    }
}