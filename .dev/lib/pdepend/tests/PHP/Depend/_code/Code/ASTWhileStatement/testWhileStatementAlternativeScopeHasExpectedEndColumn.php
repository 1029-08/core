<?php
function testWhileStatementAlternativeScopeHasExpectedEndColumn(array $values)
{
    while ($value = array_pop($values)):
        if ($value < 42):
            echo $value, PHP_EOL;
        endif;
    endwhile;
}

testWhileStatementAlternativeScopeHasExpectedEndColumn(rand(23, 64));