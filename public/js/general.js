 function removeItem(that){
    var r=confirm("Are you sure?");
    if(r){
      that.parentNode.parentNode.removeChild(that.parentNode);
    }
  }

  function removeItemHTML(){
    return "<label class='rm_item' onClick='removeItem(this)'>X</label>";
  }

  function getById(id){
  	return document.getElementById(id);
  }

  function select(name,items,slcted_item, more){

    var slct="<select name='"+name+"' "+more+">";
    for(var i in items){
      if(i==slcted_item){
        slct+="<option selected value='"+i+"'>"+items[i]+"</option>";
      }else{
        slct+="<option value='"+i+"'>"+items[i]+"</option>";
      }
    }
    return slct+"</select>";
  }

  function generalSelect(name,items,label,slcted_item,other_options){
    var slct="<select name='"+name+"' "+other_options+">";
    slct+="<option selected value=''>"+label+"</option>";
    for(var i in items){
      if(i==slcted_item){
        slct+="<option selected value='"+i+"'>"+items[i]+"</option>";
      }else{
        slct+="<option value='"+i+"'>"+items[i]+"</option>";
      }
    }
    return slct+"</select>";
  }

  function getByClass(clss){
  	return document.querySelectorAll(clss);
  }

  function clearByClass(clss){
  	var items=getByClass(clss);
  	for(var i in items){
  		items[i].innerHTML="";
  	}
  }

function showClass(clss){
  document.querySelector(clss).style.display="block";
}

function hideClass(clss){
  document.querySelector(clss).style.display="none";
}

function previewImage(input,show_id) {
  var preview = document.getElementById(show_id);
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
      preview.setAttribute('src', e.target.result);
    }
    reader.readAsDataURL(input.files[0]);
  }
}

function beginsWith(h, n, case_sensitive){

  var needle = n.trim();
  var haystack = h.trim();
  case_sensitive = (case_sensitive === true) ? true : false;

  if( case_sensitive )
    return haystack.search( needle ) === 0;
  else
    return haystack.toLowerCase().search( needle.toLowerCase() ) === 0;
}


function formatPhoneNumber(phone){
  var phone_number = phone.replace(/[^0-9]/g, '');
  var phone_number_length = phone_number.length;

  if(phone_number === "") return "";
  if(beginsWith(phone_number, "2567")){  return (phone_number_length == 12)? phone_number: ""; }
  if(beginsWith(phone_number, "2563")){  return (phone_number_length == 12)? phone_number: ""; }
  if(beginsWith(phone_number, "2564")){  return (phone_number_length == 12)? phone_number: ""; }
  if(beginsWith(phone_number,   "04")){  return (phone_number_length == 10)? "256" + phone_number.substring(1): ""; }
  if(beginsWith(phone_number,   "07")){  return (phone_number_length == 10)? "256" + phone_number.substring(1): ""; }
  if(beginsWith(phone_number,    "7")){  return (phone_number_length ==  9)? "256" + phone_number: ""; }
  return "";

}

function windPop(link) {
  window.open(link,"zzz","width=900,height=1000,menubar=no,resizable=yes,scrollbars=yes");
}