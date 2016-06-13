<?php
header( 'Content-Type: text/javascript' );
$sec =dirname($_SERVER['PHP_SELF']);
/*
(function() { 
  tinymce.PluginManager.add('pmbc_btn_logos', function( editor, url ) { 
    editor.addButton( 'pmbc_btn_logos', 
    { text: 'My test button', 
      icon: false,
      onclick: function() { 
              editor.insertContent('Hello World!');
               } 
             });
              }); 
            })();
*/
            ?>
(function() { tinymce.PluginManager.add('pmbc_btn_logos', function( editor, url ) { 
    editor.addButton( 'pmbc_btn_logos', {
            title: 'PMBc Logos',
            text: 'PMBc Logos',
            type: 'menubutton',
            icon: false,
            menu: [{
              text: 'Cairn',
              onclick: function() { editor.insertContent('<img src="<?php echo $sec."/logos/cairnpetit.png"; ?> ">'); }
            }, {
              text: 'Persée',
              onclick: function() { editor.insertContent('<img src="<?php echo $sec."/logos/perseepetit.png"; ?> ">'); }
            } , {
              text: 'CRDP',
              onclick: function() { editor.insertContent('<img src="<?php echo $sec."/logos/crdppetit.png"; ?> ">'); }               
          } , {
              text: 'Ins. Fr Educ',
              onclick: function() { editor.insertContent('<img src="<?php echo $sec."/logos/ifepetit.png"; ?> ">'); }               
          } , {
              text: 'Revues.org',
              onclick: function() { editor.insertContent('<img src="<?php echo $sec."/logos/revuesorgpetit.png"; ?> ">'); }               
          } , {
              text: 'IREM Grenoble',
              onclick: function() { editor.insertContent('<img src="<?php echo $sec."/logos/iremgrenoblepetit.png"; ?> ">'); }               
          } , {
              text: 'INRP',
              onclick: function() { editor.insertContent('<img src="<?php echo $sec."/logos/inrppetit.png"; ?> ">'); }               
          } , {
              text: 'Erudit',
              onclick: function() { editor.insertContent('<img src="<?php echo $sec."/logos/eruditpetit.png"; ?> ">'); }               
          } , {
              text: 'iRevues',
              onclick: function() { editor.insertContent('<img src="<?php echo $sec."/logos/irevuespetit.png"; ?> ">'); }               
          } , {
              text: 'Nouvelle Revue de Théologie',
              onclick: function() { editor.insertContent('<img src="<?php echo $sec."/logos/nrtpetit.png"; ?> ">'); }               
          } , {
              text: 'Via@',
              onclick: function() { editor.insertContent('<img src="<?php echo $sec."/logos/via.png"; ?> ">'); }               
          }
          ]
 
    });
  });
}) ();