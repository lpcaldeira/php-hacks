<?php

header('Access-Control-Allow-Origin: ' . (isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '"*"'));
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Content-Type');

session_start();

set_time_limit(0);

require_once $_SERVER['DOCUMENT_ROOT'] . "/configs/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/configs/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/configs/util.php";


/// Testes não feitos em Linux


// Coloca o conteúdo do word numa variável para passar pro novo documento
$arquivo = $_SERVER['DOCUMENT_ROOT']."/leodocword.docx"; 
$word = new COM("word.application", NULL, CP_UTF8) or die ("Could not initialise MS Word object.");
$word->Documents->Open(realpath($arquivo));

$nomeNovoDocumento = pathinfo($arquivo, PATHINFO_DIRNAME)."/Novo";
$nomeNovoDocumento .= pathinfo($arquivo, PATHINFO_FILENAME).".";
$nomeNovoDocumento .= pathinfo($arquivo, PATHINFO_EXTENSION);

// Pega o conteúdo do arquivo
$conteudo = (string) $word->ActiveDocument->Content;

// Altera as variáveis e salva o documento com novo nome
$conteudo = str_replace('%%variavelLeo%%','José da Silva: 228 anos', $conteudo);
$word->ActiveDocument->Content = $conteudo;
$word->ActiveDocument->SaveAs($nomeNovoDocumento);

// Fecha documento original
$word->ActiveDocument->Close(false);
// Fecha todos word abertos
$word->Quit();
// Limpa lixos de memória
$word = null;
unset($word);



die();

// Aqui ta funcionando word e rtf
$input = "C:\Users\leonardo\Desktop\leodocword.docx"; 

// starting word
$word = new COM("word.application", NULL, CP_UTF8) or die("Unable to instantiate Word");
echo "Loaded Word, version {$word->Version}<br>";

print "bring it to front<br>";
$word->Visible = 1;

print "Opened $input<br>"; 
$word->Documents->Open($input); 

print "Extrai o conteudo<br>"; 
$conteudo = (string) $word->ActiveDocument->Content;
$conteudo = str_replace('%%variavelLeo%%','José da Silva: 28 anos',$conteudo);

$word->ActiveDocument->Content = $conteudo;
$word->Documents[1]->SaveAs("C:\Users\leonardo\Desktop\leodocword atualizado.docx");
$word->Documents->Close(false);

//closing word
$word->Quit();

//free the object
$word = null;


die();

// Funcionando para RTF e TXT
$vars = array('%%variavelLeo%%'=>'José da Silva: 28 anos');
//$doc_file = 'C:\Users\leonardo\Desktop\leodocrtf.rtf';
$doc_file = 'C:\Users\leonardo\Desktop\Manipulação de documentos PHP - Copia.txt';
populate_RTF($vars, $doc_file);

function populate_RTF($vars, $doc_file) {
     
    $arquivo = fopen($doc_file,'r+'); 
    $replacements = array ('\\' => "\\\\",
                           '{'  => "\{",
                           '}'  => "\}");
    
    $document = file_get_contents($doc_file);
    if(!$document) {
        return false;
    }
    
    foreach($vars as $key=>$value) {
        // $search = "%%".strtoupper($key)."%%";
        $search = str_replace('%%','', $key);
        
        foreach($replacements as $orig => $replace) {
            $value = str_replace($orig, $replace, $value);
        }
        
        $document = str_replace($search, $value, $document);
        $document = str_replace('%%','', $document);
    }
    
    //return $document;
    if (!fwrite($arquivo, $document)) {
        die('Não foi possível atualizar o arquivo.');
    } 
    echo 'Arquivo atualizado com sucesso'; 
    fclose($arquivo);
}


die();

// abaixo só funcionou com .txt

// Abre o arquivo colocando o ponteiro de escrita no final 
$arquivo = fopen('C:\Users\leonardo\Desktop\leodocpdf.pdf','r+'); 
if ($arquivo) { 
    $string = '';
    while(true) { 
        $linha = fgets($arquivo);
        if ($linha==null){
            break;
        }
        // busca na linha atual o conteudo que vai ser alterado
        if(preg_match("/@@variavelLeo@@/", $linha)) { 
            $string .= str_replace("@@variavelLeo@@", "José da Silva: 28 anos de ", $linha); 
        } 
        else { 
            // vai colocando tudo numa nova string 
            $string.= $linha; 
        } 
    } 
    // move o ponteiro para o inicio pois o ftruncate() nao fara isso 
    rewind($arquivo); 
    // truca o arquivo apagando tudo dentro dele 
    ftruncate($arquivo, 0); 
    // reescreve o conteudo dentro do arquivo 
    if (!fwrite($arquivo, $string)) {
        die('Não foi possível atualizar o arquivo.');
    } 
    echo 'Arquivo atualizado com sucesso'; 
    fclose($arquivo); 
}

?>