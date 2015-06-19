<?php

//taken from http://d.hatena.ne.jp/plasticscafe/20081005/1223231105

// Mecab　Extentionを指定
define('MECAB_MODULE', 'mecab.so');
// Mecab用辞書ファイルを指定
define('DIC_PATH', '/usr/local/lib/mecab/dic/ipadic');

class Mecab_Analyze
{
    private $text;
    private $pos_array;
    private $threshold;
    
    public function __construct($pos_array=null, $threshold=5)
    {
        // MECABモジュールの読み込み
        //dl(MECAB_MODULE);
        // 辞書ファイルの読み込み先を設定
        $dic = DIC_PATH;
        if (!empty($dic))
        {
            if (!file_exists($dic))
            {
                print("Error: dic file not exist");
                return false;
            }
            //設定オプションとして辞書ファイルのパスを設定
            ini_set('mecab.default_dicdir', $dic);
        }
        
        // 解析対象の品詞IDを設定
        /**
        * 初期値として設定する計測する品詞（あまり根拠無く、適当に選別）
        * 名詞,サ変接続,*,* 36
        * 名詞,ナイ形容詞語幹,*,* 37
        * 名詞,一般,*,* 38
        * 名詞,引用文字列,*,* 39
        * 名詞,形容動詞語幹,*,* 40
        * 名詞,固有名詞,一般,* 41
        * 名詞,固有名詞,人名,一般 42
        * 名詞,固有名詞,人名,姓 43
        * 名詞,固有名詞,人名,名 44
        * 名詞,固有名詞,組織,* 45
        * 名詞,固有名詞,地域,一般 46
        * 名詞,固有名詞,地域,国 47
        */
        if(is_null($pos_array))
        {
            $this->pos_array = array(36,37,38,39,40,41,42,43,44,45,46,47);       
        }
        else
        {
            $this->pos_array = $pos_array;
        }
        // 出現単語の閾値を設定
        $this->threshold = $threshold;
    }

    private function __posCheck($pos_id)
    {
        if(in_array($pos_id, $this->pos_array))
        {
            return true;
        }
        return false;
    }
    
    private function __multiArraySearch($needle, $key, $array=array())
    {
        if(0 < count($array))
        {
            foreach($array as $array_key => $value)
            {
                if($value[$key] == $needle)
                {
                    return $array_key;
                }
            }
        }
        return false;
    }

    private function __parseText()
    {
        $text = $this->text;        
        // Mecabオブジェクトを生成
        $mecab = new MeCab_Tagger();
        //文字列をパース
        $node=$mecab->parseToNode($text);  
        /**
        * $nodeの要素メモ
        * -- 基本情報 --
        * [surface]         : 形態素の文字列情報
        * [feature]         : CSV で表記された素性情報
        * [id]              : 形態素に付与される ユニークID
        * [length]          : 形態素の長さ
        * [rlength]         : 形態素の長さ(先頭のスペースを含む)
        * [rcAttr]          : 右文脈 id
        * [lcAttr]          : 左文脈 id
        * [posid]           : 形態素(品詞) ID
        * [char_type]       : 文字種情報
        * [stat]            : 形態素の種類: 以下のマクロの値
        *                       #define MECAB_NOR_NODE  0
        *                       #define MECAB_UNK_NODE  1
        *                       #define MECAB_BOS_NODE  2
        *                       #define MECAB_EOS_NODE  3
        * [isbest]          : ベスト解の場合 1, それ以外 0
        * [sentence_length] : (要確認)解析対象文章の長さ？ BOS nodeのみ？
        *
        * -- alpha, beta, prob は -l 2 オプションを指定した時に定義 --
        * [alpha]           : forward backward の foward log 確率
        * [beta]            : forward backward の backward log 確率
        * [prob]            : 周辺確率
        *
        * -- 解析のコスト関連の値 --
        * [wcost]           : 単語生起コスト
        * [cost]            : 累積コスト
        */
        //////////////////////////////
        //// 各ノードを解析
        // 格納用の配列を初期化
        $array = array();
        // 解析結果を配列に格納
        while($node)
        {
            $array_tmp = $node->toArray();
            if($this->__posCheck($array_tmp['posid']))
            {
                $result = $this->__multiArraySearch($array_tmp['surface'], 'surface', $array);
                if($result !== false)
                {
                    $array[$result]['num']++;
                }
                else
                {
                    // 配列の要素から使う物のみを選別
                    $array_item['surface'] = $array_tmp['surface'];
                    $array_item['posid'] = $array_tmp['posid'];
                    $array_item['length'] = $array_tmp['length'];
                    $array_item['num'] = 1;
                    // 配列を追加
                    $array[] = $array_item;
                }
            }
            // 配列格納処理が完了したら次ノードへ
            $node = $node->getNext();
        }
        // 格納された配列をリターン
        return $array;
    }

    private function __sortFunction($a, $b)
    {
        if ($a['num'] == $b['num'])
        {
            return 0;
        }
        elseif($a['num'] < $b['num'])
        {
            return 1;
        }
        else
        {
            return -1;
        }
    }

    public function getResult($text, $sort_flag=true)
    {
        // 解析対象のテキストを設定
        if(isset($text) || $text !=  "")
        {
            $this->text = $text;
        }
        else
        {
            print("Error: Not Input Text");
            return false;
        }
        $array = $this->__parseText();

        if($sort_flag)
        {
            uasort($array, array($this, "__sortFunction"));
        }

        return $array;
    }
    //taken from http://www.pahoo.org/e-soul/webtech/php03/php03-13-01.shtm
    public function count_weight($items) {
	$ret = 9;
	foreach ($items as $word)	$ret += mb_strlen($word) * mb_strlen($word);

	return $ret;
	}
	//taken from http://www.pahoo.org/e-soul/webtech/php03/php03-13-01.shtm
	public function similar_mecab($sour, $dest) {
	$items_sour = array();
	$items_dest = array();

	parsing_mecab($sour, $items_sour);
	parsing_mecab($dest, $items_dest);

	$result = count_weight(array_intersect($items_sour, $items_dest));
	$result = (double)$result / count_weight($items_dest);
	if ($result > 1)	$result = 1;

	return $result;
	}

}


/**
* テスト実行用のスクリプト
*/
/*
$text = "Mecabを使ってPHPで形態素解析してみるテストですが、PHPではどんな塩梅？";
$ma = new Mecab_Analyze();
$result = $ma->getResult($text);

foreach($result as $word)
{
    print_r($word);
}
*/
?>