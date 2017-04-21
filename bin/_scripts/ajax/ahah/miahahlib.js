// miAHAHlib.js
function llamarAHAH(url, elementoPag, mensLlamada) 
 {
     document.getElementById(elementoPag).innerHTML = mensLlamada;
     try 
	  {
       http = new XMLHttpRequest(); /* p.e. Firefox */
      } 
	 catch(e) 
	  {
       try 
	    {
         http = new ActiveXObject("Msxml2.XMLHTTP");
         /* algunas versiones IE */
        } 
	   catch (e) 
	    {
         try 
		  {
           http = new ActiveXObject("Microsoft.XMLHTTP");
           /* algunas versiones IE */
          } 
		 catch (E) 
		  {
           http = false;
          }
         }
       }
     http.onreadystatechange = function() {respuestaAHAH(elementoPag);};
     http.open("GET",url,true);
     http.send(null);
  }

function respuestaAHAH(elementoPag) 
 {
   var resultado = '';
   if(http.readyState == 4) 
    {
      if(http.status == 200) 
	   {
         resultado = http.responseText;
         document.getElementById(elementoPag).innerHTML = resultado;
       }
    }
  }

