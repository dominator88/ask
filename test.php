<?php
set_time_limit (0);       // 设置运行时间

if (!class_exists ("gtk2"))           // 判断是否有GTK模块

   
    echo    extension_loaded ("php_gtk2.dll");
  exit; 

$window = &new GTKWindows(1);              // 建一个窗口
$window->set_uposition (100, 100);        //  窗口出现位置
$window->set_usize ((gdk::screen_width()-200), (gdk::screen_height()-150));  // 窗口大小
$window->set_title ("WINDOWS");         // 设置窗口标题
$window->connect_object ('destroy', array ('gtk', 'main_quit'));     // 注册窗口的事件

$vbox = &new GtkVBox ();           
$hbox = &new GtkHBox ();         
$window->add ($vbox);


$menuBar = &new GtkMenuBar ();               // 创建菜单
$vbox->pack_start ($menuBar, false, false, 0);

$file = &new GtkMenuItem ("File");
$menuBar->append ($file);

$fileMenu = &new GtkMenu ();
$open = &new GtkMenuItem ("Open");
$save = &new GtkMenuItem ("Save");
$line = &new GtkMenuItem ();
$line->set_sensitive (true);
$exit = &new GtkMenuItem ("Exit");
$fileMenu->append ($open);
$open->connect_object ('activate', 'showFileSelection');        
$fileMenu->append ($save);
$save->connect_object ('activate', 'saveFile');
$fileMenu->append ($line);
$fileMenu->append ($exit);
$exit->connect_object ('activate', array ('gtk', 'main_quit'));

$file->set_submenu ($fileMenu);

$scroll = &new GtkScrolledWindow ();
$scroll->set_border_width (8);
$scroll->set_policy (GTK_POLICY_AUTOMATIC, GTK_POLICY_AUTOMATIC);
$hbox->pack_start ($scroll, true, true, 0);
$vbox->pack_start ($hbox, true, true, 1);

$text = &new GtkText ();
$text->set_editable (true);
$text->set_word_wrap (true);
$scroll->add ($text);

function showFileSelection ()           // 文件选择函数
{
    $file = &new GtkFileSelection ("File Selection");
    $ok_button = $file->ok_button;
    $ok_button->connect ('clicked', 'openFile', $file);
    $ok_button->connect_object ('clicked', array ($file, 'destroy'));
    $cancel_button = $file->cancel_button;
    $cancel_button->connect_object ('clicked', array ($file, 'destroy'));
    $file->show ();
}

$filePath = null;
function openFile ($button, $f)          // 打开文件的函数
{
    GLOBAL $text, $save, $filePath;
    $filePath = $f->get_filename ();
    if (is_file ($filePath))
    {
        $fp = fopen ($filePath, 'r');
        while (!feof ($fp))
            $str .= fgets ($fp, 1024);
        $text->insert (null, null, null, $str);
        fclose ($fp);
        return $filePath;
    }
}

function saveFile ()                // 保存文件的函数
{
    GLOBAL $filePath, $text;
    if (is_file ($filePath))
    {
        $str = $text->get_chars (0, -1);
        $fp = fopen ($filePath, 'w');
        fwrite ($fp, $str);
        fclose ($fp);
    }
    return;
}

$window->show_all ();             // 显示窗体内的所有控件
gtk::main ();                      // 最重要的一句，不可少的
?>