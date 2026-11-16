<?php
require '/home/zdann/Documents/ChatGPT/epl/point-of-sales/vendor/autoload.php';
$root='/home/zdann/Documents/ChatGPT/epl/point-of-sales/';
$rows=json_decode(file_get_contents($root.'../output/audit-pos/inventory.json'),true);
$parser=(new PhpParser\ParserFactory)->createForNewestSupportedVersion();$result=[];$errors=[];
$finder=new PhpParser\NodeFinder;
foreach($rows as $r){$file=$r['path'];if(!str_ends_with($file,'.php')||str_contains($file,'.blade.'))continue;
try{$ast=$parser->parse(file_get_contents($root.$file));$methods=$finder->findInstanceOf($ast,PhpParser\Node\Stmt\ClassMethod::class);
foreach($methods as $m){$nodes=$finder->find($m->stmts??[],fn($n)=>$n instanceof PhpParser\Node\Stmt\If_||$n instanceof PhpParser\Node\Stmt\ElseIf_||$n instanceof PhpParser\Node\Stmt\For_||$n instanceof PhpParser\Node\Stmt\Foreach_||$n instanceof PhpParser\Node\Stmt\While_||$n instanceof PhpParser\Node\Stmt\Do_||$n instanceof PhpParser\Node\Stmt\Catch_||$n instanceof PhpParser\Node\Expr\Ternary||$n instanceof PhpParser\Node\Expr\BinaryOp\BooleanAnd||$n instanceof PhpParser\Node\Expr\BinaryOp\BooleanOr);
$result[]=['file'=>$file,'method'=>(string)$m->name,'start'=>$m->getStartLine(),'end'=>$m->getEndLine(),'physical_lines'=>$m->getEndLine()-$m->getStartLine()+1,'parameters'=>count($m->params),'decision_nodes_including_closures'=>count($nodes)];}
}catch(Throwable $e){$errors[]=[$file,$e->getMessage()];}}
file_put_contents($root.'../output/audit-pos/php-method-metrics.json',json_encode(['method'=>$result,'parse_errors'=>$errors],JSON_PRETTY_PRINT));
usort($result,fn($a,$b)=>$b['physical_lines']<=>$a['physical_lines']);foreach(array_slice($result,0,35) as $r)echo json_encode($r)."\n";
