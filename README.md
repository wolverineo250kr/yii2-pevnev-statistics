# yii2-pevnev-statistics
<p>
<h2>Install</h2>
<b>composer require "wolverineo250kr/yii2-pevnev-statistics":"*"</b>
<br/>
<br/>
main.php
<br/>
<br/>
return [<br/>...<br/> 
    'bootstrap' => ['log', 'assetsAutoCompress', 'statistics'],<br/>
	    'modules' => [<br/>
        'blog' => [<br/>
                    'class' => 'wolverineo250kr\statistics\modules\frontend\Module',<br/>
        ],<br/>
		],<br/>