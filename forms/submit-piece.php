<?php

$this->execPieceByName('ff_InitLib');

//Validation
if( ff_getSubmit('validation') != '' ){
	exit; // well, it should be empty
}

//Echo form to Stars Lead Manager
echo '
<form method="post" name="myresubmit" name="myresubmit" action="https://stars.trainingmasters.com/INBLEADS.pgm">
<input type="hidden" id="task" name="task" value="endadd">
<input type="hidden" id="SelComp" name="SelComp" value="'.htmlentities( ff_getSubmit('SelComp') ).'"/>
<input type="hidden" id="SelResponse" name="SelResponse" value="'.htmlentities( ff_getSubmit('SelResponse') ).'"/>
<input type="hidden" id="ReturnURL" name="ReturnURL" value="'.htmlentities( ff_getSubmit('ReturnURL') ).'"/>
<input type="hidden" id="BNJJTX" name="BNJJTX" value="'.htmlentities( ff_getSubmit('BNJJTX') ).'"/>
<input type="hidden" id="BNJKTX" name="BNJKTX" value="'.htmlentities( ff_getSubmit('BNJKTX') ).'"/>
<input type="hidden" id="BNJITX" name="BNJITX" value="'.htmlentities( ff_getSubmit('BNJITX') ).'"/>
<input type="hidden" id="BNQ0NB" name="BNQ0NB" value="'.htmlentities( ff_getSubmit('BNQ0NB') ).'"/>
<input type="hidden" id="BNJLTX" name="BNJLTX" value="'.htmlentities( ff_getSubmit('BNJLTX') ).'"/>
<input type="hidden" id="BNJMTX" name="BNJMTX" value="'.htmlentities( ff_getSubmit('BNJMTX') ).'"/>
<input type="hidden" id="BNJNTX" name="BNJNTX" value="'.htmlentities( ff_getSubmit('BNJNTX') ).'"/>
<input type="hidden" id="BNC0CD" name="BNC0CD" value="'.htmlentities( ff_getSubmit('BNC0CD') ).'"/>
<input type="hidden" id="BNC1CD" name="BNC1CD" value="'.htmlentities( ff_getSubmit('BNC1CD') ).'"/>
<input type="hidden" id="BNQ6NB" name="BNQ6NB" value="'.htmlentities( ff_getSubmit('BNQ6NB') ).'"/>
<input type="hidden" id="BNC2CD" name="BNC2CD" value="'.htmlentities( ff_getSubmit('BNC2CD') ).'"/>
<input type="hidden" id="BNHXCD" name="BNHXCD" value="'.htmlentities( ff_getSubmit('BNHXCD') ).'"/>
<input type="hidden" id="LM5EM1" name="LM5EM1" value="'.htmlentities( ff_getSubmit('LM5EM1') ).'"/>
<input type="hidden" id="LMCELLA" name="LMCELLA" value="'.htmlentities( ff_getSubmit('LMCELLA') ).'"/>
<input type="hidden" id="OPT_IN_TEXT" name="OPT_IN_TEXT" value="'.htmlentities( ff_getSubmit('OPT_IN_TEXT') ).'"/>
</form>
<script>
document.myresubmit.submit();
</script>
';

exit;

?>