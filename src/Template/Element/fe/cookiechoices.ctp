<?php echo $this->Html->script('cookiechoices'); ?>
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function(event) {
cookieChoices.showCookieConsentDialog("Questo sito utilizza i cookie per fornire la migliore esperienza di navigazione ed erogare i servizi. Se continui ad utilizzare questo sito accetti l'utilizzo dei cookie",
'Chiudi', 'Maggiori Informazioni', 'https://www.iubenda.com/privacy-policy/94904265');
});
</script>  
<style>
#cookieChoiceInfo {
  box-shadow: 10px 21px 9px 28px #888888;
  transition: background 0.5s ease-in-out 0s, padding 0.5s ease-in-out 0s;
}
#cookieChoiceInfo > span {
  line-height: 20px;
  color: white;
}
#cookieChoiceInfo > a {
  color: white;
}
#cookieChoiceDismiss {
  margin: 10px 0 0 0  !important;
  text-align: center !important;
  background: #31658e;
  -webkit-border-radius: 5;
  -moz-border-radius: 5;
  border-radius: 5px;
  color: #ffffff;
  padding: 3px 8px;
  text-decoration: none;
}
#cookieChoiceDismiss:hover {
  background: #919950;
  text-decoration: none;
}
</style>