/*!
 * jQuery Chord Transposer plugin v1.0
 * http://codegavin.com/projects/transposer
 *
 * Copyright 2010, Jesse Gavin
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://codegavin.com/license
 *
 * Date: Sat Jun 26 21:27:00 2010 -0600
 */


(function($) {
	
  $.fn.transpose = function(options) {
    var opts = $.extend({}, $.fn.transpose.defaults, options);
    

    var keyType = null;
    var currentKey = null;
	var lastKey = null;
	var firstKey = null;
	var keys = [
	  { name: 'Abb',		value: 11,	type: 'F',	STT: 0 },// 0
      { name: 'Ab',			value: 0,	type: 'F',	STT: 0 },// 0
      { name: 'A',			value: 1,	type: 'N',	STT: 0 },// 1
      { name: 'A#',			value: 2,	type: 'S',	STT: 0 },// 2
	  { name: 'A##',		value: 3,   type: 'F',	STT: 0 },// 0
	  { name: 'Bbb',		value: 1,   type: 'F',	STT: 1 },// 3
      { name: 'Bb',			value: 2,   type: 'F',	STT: 1 },// 3
      { name: 'B',			value: 3,   type: 'N',	STT: 1 },// 4
      { name: 'B#',			value: 4,   type: 'N',	STT: 1 },// 5
	  { name: 'B##',		value: 5,   type: 'F',	STT: 1 },// 3
	  { name: 'Cbb',		value: 2,   type: 'F',	STT: 2 },// 6
      { name: 'Cb',			value: 3,   type: 'F',	STT: 2 },// 6
      { name: 'C',			value: 4,   type: 'N',	STT: 2 },// 7
      { name: 'C#',			value: 5,   type: 'S',	STT: 2 },// 8
	  { name: 'C##',		value: 6,   type: 'S',	STT: 2 },// 8
	  { name: 'Dbb',		value: 4,   type: 'F',	STT: 3 },// 9
      { name: 'Db',			value: 5,   type: 'F',	STT: 3 },// 9
      { name: 'D',			value: 6,   type: 'N',	STT: 3 },// 10
      { name: 'D#',			value: 7,   type: 'S',	STT: 3 },// 11
	  { name: 'D##',		value: 8,   type: 'F',	STT: 3 },// 9
	  { name: 'Ebb',		value: 6,   type: 'F',	STT: 4 },// 12
      { name: 'Eb',			value: 7,   type: 'F',	STT: 4 },// 12
      { name: 'E',			value: 8,   type: 'N',	STT: 4 },// 13
      { name: 'E#',			value: 9,   type: 'F',	STT: 4 },// 14
	  { name: 'E##',		value: 10,	type: 'SS',	STT: 4 },// 15
	  { name: 'Fbb',		value: 7,   type: 'F',	STT: 5 },// 14
	  { name: 'Fb',			value: 8,   type: 'F',	STT: 5 },// 14
      { name: 'F',			value: 9,   type: 'N',	STT: 5 },// 16
      { name: 'F#',			value: 10,  type: 'S',	STT: 5 },// 17
	  { name: 'F##',		value: 11,  type: 'S',	STT: 5 },// 17
	  { name: 'Gbb',		value: 9,	type: 'F',	STT: 6 },// 18
      { name: 'Gb',			value: 10,  type: 'F',	STT: 6 },// 18
      { name: 'G',			value: 11,  type: 'N',	STT: 6 },// 19
      { name: 'G#',			value: 0,   type: 'S',	STT: 6 },// 20
	  { name: 'G##',		value: 1,   type: 'S',	STT: 6 }// 20
    ];
	// Get Key by Name from Keys array  
    var getKeyByName = function (name) {
        if (name.charAt(name.length-1) == "m") {
          name = name.substring(0, name.length-1);
        }
        for (var i = 0; i < keys.length; i++) {
            if (name == keys[i].name) {
                return keys[i];
            }
        }
    };
	// tinhchat = 1 -> gam thứ
    var getTinhChat = function (name) {
		var sob = name.search("m");
        if (sob > 0) {
          return tinhchat = 1;
        }
		return tinhchat = 0;
	};
	 var getSlash = function (name) {
		var s = name.search("/");
        if (s > 0) {
          return slash = 1;
        }
		return slash = 0;
	};
	
    var getChordRoot = function (input) {
		//alert("input: "+ input)
        if (input.length > 1 && (input.charAt(2) == "b" || input.charAt(2) == "#"))
			return input.substr(0, 3);
		else if (input.length > 1 && (input.charAt(1) == "b" || input.charAt(1) == "#"))

			return input.substr(0, 2);
        else
		    return input.substr(0, 1);
    };

    var getNewKey = function (oldKey, delta, deltaSTT, targetKey, tinhchat) {
		
        var keyValue = getKeyByName(oldKey).value + delta;
        var keySTT = getKeyByName(oldKey).STT + deltaSTT;
		//alert(oldKey);
		//var tinhchat = getTinhChat(OldKey);
		//var tinhchat2 = getTinhChat(TargetKey);
		//alert("OldKey:" + tinhchat);
		//alert("TargetKey:" + tinhchat2);
        if (keyValue > 11) {
            keyValue -= 12;
        } else if (keyValue < 0) {
            keyValue += 12;
        }
        if (keySTT > 6) {
            keySTT -= 7;
        } else if (keySTT < 0) {
            keySTT += 7;
        }
       //var tinhchat = getTinhChat(key[i]);
	    //alert("Truong 0 - Thu 1 - Trong ham getNewKey: " + keyType + "\n Lastkey" + lastKey + "\n firstKey" + firstKey); 
		var i=0;
		if (keyValue == 11 && keySTT == 0){return keys[0];}
		if (keyValue == 0 && keySTT == 0){return keys[1];}
		if (keyValue == 1 && keySTT == 0){return keys[2];}			
		if (keyValue == 2 && keySTT == 0){return keys[3];}
		if (keyValue == 3 && keySTT == 0){return keys[4];}
		if (keyValue == 1 && keySTT == 1){return keys[5];}
		if (keyValue == 2 && keySTT == 1){return keys[6];}
		if (keyValue == 3 && keySTT == 1){return keys[7];}
		if (keyValue == 4 && keySTT == 1){return keys[8];}	
		if (keyValue == 5 && keySTT == 1){return keys[9];}
		if (keyValue == 2 && keySTT == 2){return keys[10];}
		if (keyValue == 3 && keySTT == 2){return keys[11];}			
		if (keyValue == 4 && keySTT == 2){return keys[12];}
		if (keyValue == 5 && keySTT == 2){return keys[13];}
		if (keyValue == 6 && keySTT == 2){return keys[14];}			
		if (keyValue == 4 && keySTT == 3){return keys[15];}
		if (keyValue == 5 && keySTT == 3){return keys[16];}
		if (keyValue == 6 && keySTT == 3){return keys[17];}
		if (keyValue == 7 && keySTT == 3){return keys[18];}
		if (keyValue == 8 && keySTT == 3){return keys[19];}
		if (keyValue == 6 && keySTT == 4){return keys[20];}	
		if (keyValue == 7 && keySTT == 4){return keys[21];}
		if (keyValue == 8 && keySTT == 4){return keys[22];}
		if (keyValue == 9 && keySTT == 4){return keys[23];}			
		if (keyValue == 10 && keySTT == 4){return keys[24];}
		if (keyValue == 7 && keySTT == 5){return keys[25];}
		if (keyValue == 8 && keySTT == 5){return keys[26];}			
		if (keyValue == 9 && keySTT == 5){return keys[27];}
		if (keyValue == 10 && keySTT == 5){return keys[28];}
		if (keyValue == 11 && keySTT == 5){return keys[29];}
		if (keyValue == 9 && keySTT == 6){return keys[30];}
		if (keyValue == 10 && keySTT == 6){return keys[31];}
		if (keyValue == 11 && keySTT == 6){return keys[32];}	
		if (keyValue == 0 && keySTT == 6){return keys[33];}
		if (keyValue == 1 && keySTT == 6){return keys[34];}			

		if ((keyType == 0 && lastKey == "Db") || (keyType == 1 && lastKey == "Bb")) {
			// alert(lastKey);
			switch (keyValue) {
			 case 5: {return keys[9];}
			 case 10: {return keys[18];}
			 case 0: {return keys[0];}
			 case 2: {return keys[3];}			 
			 case 7: {return keys[12];}
			 case 9: {return keys[16];}
			 case 4: {return keys[7];}
			 default:
					  for (;i<keys.length;i++) {
						if (keys[i].value == keyValue && keys[i].type == "F") {
						  return keys[i];
						}
					  }
				
			 }
			}
		else {
			if (keyValue == 0 || keyValue == 2 || keyValue == 5 || keyValue == 7 || keyValue == 10) {
				// Return the Flat or Sharp Key
				switch(targetKey.name) {
				  case "A":
				  case "A#":
				  case "B":
				  case "C":
				  case "C#":
				  case "D":
				  case "D#":
				  case "E":
				  case "E#":
				  case "G":
				  case "G#":
					  for (;i<keys.length;i++) {
						if (keys[i].value == keyValue && keys[i].type == "S") {
						  return keys[i];
						}
					  }
				  default:
					  for (;i<keys.length;i++) {
						if (keys[i].value == keyValue && keys[i].type == "F") {
						  return keys[i];
						}
					  }
				}
			}
			else {
				// Return the Natural Key
				for (;i<keys.length;i++) {
				  if (keys[i].value == keyValue) {
					return keys[i];
				  }
				}
			}			};
    };

    var getChordType = function (key) {
        switch (key.charAt(key.length - 1)) {
            case "b":
                return "F";
            case "#":
                return "S";
            default:
              return "N";
        }

    };

    var getDelta = function (oldIndex, newIndex) {
        if (oldIndex > newIndex)
            return 0 - (oldIndex - newIndex);
        else if (oldIndex < newIndex)
            return 0 + (newIndex - oldIndex);
        else
            return 0;
    };
	var getDeltaSTT = function (oldIndex, newIndex) {
        if (oldIndex > newIndex)
            return 0 - (oldIndex - newIndex) ;
        else if (oldIndex < newIndex)
            return 0 + (newIndex - oldIndex) ;
        else
            return 0;
    };	
    var transposeSong = function (target, key, tinhchat) {
				tinhchat = getTinhChat(key);
				//alert("Key: "+key+" va tinh chat key mo'i : " + tinhchat + " keyTpye: " + keyType);
				lastKey = key;
		
		//var chordsList = [];
   	   
        var newKey = getKeyByName(key);
        if (currentKey.name == newKey.name) {
          return;
        }

        var delta = getDelta(currentKey.value, newKey.value);
        var deltaSTT = getDeltaSTT(currentKey.STT, newKey.STT);
        
        $("span.c", target).each(function (i, el) {
            transposeChord(el, delta, deltaSTT, newKey);
        });
		//lastKey = currentKey;
        currentKey = newKey;
        chordsList = $.unique(chordsList);
        $(window).chordsLoad(chordsList); ///Update Chords Ajax to Client
        //alert (chordsList);
		//alert("lastKey:" + newKey);
    };

    var transposeChord = function (selector, delta, deltaSTT, targetKey, tinhchat) {
        var el = $(selector); 
        var oldChord = el.text();
		//alert(oldChord);
		var slash = getSlash(oldChord);
		//alert("slash = "+slash);
		if (slash == 1) { 
		var oldChords = oldChord.split("/");
		//for (var i = 0; i < oldChords.length ; i++) alert(oldChords[i]);
		oldChord = oldChords[0];
		oldChord1 = oldChords[1];
		var oldChordRoot = getChordRoot(oldChord);
		
		if (oldChordRoot == "") return;
			var newChordRoot = getNewKey(oldChordRoot, delta, deltaSTT, targetKey, tinhchat);
			var newChord = newChordRoot.name + oldChord.substr(oldChordRoot.length);
			var oldChordRoot1 = getChordRoot(oldChord1);
		if (oldChordRoot1 == "") return;
			var newChordRoot1 = getNewKey(oldChordRoot1, delta, deltaSTT, targetKey, tinhchat);
			var newChord1 = newChordRoot1.name + oldChord1.substr(oldChordRoot1.length);
			newChord = newChord+"/"+newChord1;
			//alert("Sau transpose "+ newChord);
			chordsList.push(newChord);
			el.text(newChord);
			return newChord;
		}
		else{
			var oldChordRoot = getChordRoot(oldChord);
			if (oldChordRoot == "") return;
			var newChordRoot = getNewKey(oldChordRoot, delta, deltaSTT, targetKey, tinhchat);
			var newChord = newChordRoot.name + oldChord.substr(oldChordRoot.length);
			chordsList.push(newChord);
			el.text(newChord);
			return newChord;
		}
		
        var sib = el[0].nextSibling;
        if (sib && sib.nodeType == 3 && sib.nodeValue.length > 0 && sib.nodeValue.charAt(0) != "/" && sib.nodeValue.charAt(0) != "\(") {
            var wsLength = getNewWhiteSpaceLength(oldChord.length, newChord.length, sib.nodeValue.length);
            sib.nodeValue = makeString(" ", wsLength);
        }

    };

    var getNewWhiteSpaceLength = function (a, b, c) {
        if (a > b)
            return (c + (a - b));
        else if (a < b)
            return (c - (b - a));
        else
            return c;
    };

    var makeString = function (s, repeat) {
        var o = [];
        for (var i = 0; i < repeat; i++) o.push(s);
        return o.join("");
    }
    
    
    var isChordLine = function (input) {
	    var tokens = input.replace(/\s+/, " ");//.split(" ");
		tokens = tokens.replace(/\t+/, " ");
		tokens = tokens.replace(/\+/, " ");
		tokens = tokens.replace(/\(/, "");
		tokens = tokens.replace(/\)/, "");
		//tokens = tokens.replace(/\//, "");
		
		tokens = tokens.split(" ");

        // Try to find tokens that aren't chords
        // if we find one we know that this line is not a 'chord' line.
        for (var i = 0; i < tokens.length; i++) {
        	//if (kq < 1 || kq2 < 1 )
			if (!$.trim(tokens[i]).length == 0 && !tokens[i].match(opts.chordRegex))
                return false;
        }
        return true;
    };
    
    var wrapChords = function (input) {
		
		var output = input.replace(opts.chordReplaceRegex, "<span class='c' title='$4'>$1</span>");
        return output.split("<span class='c' title=''></span>").join("");
        //return input;
    };
    
    
    return $(this).each(function() {
    
      
      var startKey = $(this).attr("data-key");
	  var tinhchat = getTinhChat(startKey);
	  keyType = tinhchat;
	  firstKey = $(this).attr("data-key");
	  //alert("Tinh chat startKey: "+ keyType + "\n" + "The first key:" + firstKey);
	  
	  var sob = startKey.search("m");
      if (!startKey || $.trim(startKey) == "") {
        startKey = opts.key;
      }

      if (!startKey || $.trim(startKey) == "") {
        throw("Starting key not defined.");
        return this;
      }
      
      currentKey = getKeyByName(startKey);

      // Build tranpose links ===========================================
	  if (keyType == 1) { //---> là gam thứ
  		  var mkeys = [
		 // { name: 'Ab',  value: 0,   type: 'F' },
		  { name: 'A',   value: 1,   type: 'N' },
		  //{ name: 'A#',  value: 2,   type: 'S' },
		  { name: 'Bb',  value: 2,   type: 'F' },
		  { name: 'B',   value: 3,   type: 'N' },
		  { name: 'C',   value: 4,   type: 'N' },
		  { name: 'C#',  value: 5,   type: 'S' },
		  { name: 'D',   value: 6,   type: 'N' },
		  { name: 'D#',  value: 7,   type: 'S' },
		  { name: 'Eb',  value: 7,   type: 'F' },
		  { name: 'E',   value: 8,   type: 'N' },
		  { name: 'F',   value: 9,   type: 'N' },
		  { name: 'F#',  value: 10,  type: 'S' },
		  { name: 'G',   value: 11,  type: 'N' },
		  { name: 'G#',  value: 0,   type: 'S' }
		 ];
		 var info = "<div class='transpose-keys'> Abm, Am, A#m, Bbm, Bm, Cm, C#m, Dm, D#m, Ebm, Em, Fm, F#m, Gm, G#m</div>";
		 }
	  else {
		var mkeys = [
		  { name: 'Ab',  value: 0,   type: 'F' },
		  { name: 'A',   value: 1,   type: 'N' },
		  { name: 'Bb',  value: 2,   type: 'F' },
		  { name: 'B',   value: 3,   type: 'N' },
          //{ name: 'Cb',  value: 3,   type: 'F' },	  
		  { name: 'C',   value: 4,   type: 'N' },
          //{ name: 'C#',  value: 5,   type: 'S' },
		  { name: 'Db',  value: 5,   type: 'F' },
		  { name: 'D',   value: 6,   type: 'N' },
		  { name: 'Eb',  value: 7,   type: 'F' },
		  { name: 'E',   value: 8,   type: 'N' },
		  { name: 'F',   value: 9,   type: 'N' },
		  { name: 'F#',  value: 10,  type: 'S' },
		  { name: 'Gb',  value: 10,  type: 'F' },
		  { name: 'G',   value: 11,  type: 'N' },
        ];
		var info = "<div class='transpose-keys'> Ab, A, Bb, B, Cb, C, C#, Db, D, Eb, E, F, F#, Gb, G </div>";
	   }
	  var keyLinks = [];
	  		      keyLinks.push("<span class='c'>" + info + "</span>");

      $(mkeys).each(function(i, key) {
          if (currentKey.name == key.name) {
              keyLinks.push("<a href='#' class='selected'>" + key.name + "</a>");
		  }
          else
              keyLinks.push("<a href='#'>" + key.name + "</a>");

      });


      var $this = $(this);
      var keysHtml = $("<div class='transpose-keys'></div>");
      keysHtml.html(keyLinks.join(""));
      $("a", keysHtml).click(function(e) {
          e.preventDefault();
		  //alert("transposeSong: " + tinhchat);
          transposeSong($this, $(this).text(),tinhchat);
          $(".transpose-keys a").removeClass("selected");
          $(this).addClass("selected");
          return false;
      });
      
      $(this).before(keysHtml);

      var output = [];
      var lines = $(this).text().split("\n");
      var line, tmp = "";

      for (var i = 0; i < lines.length; i++) {
          line = lines[i];
		  var kq=(line.search(/ /i));
		  var kq2=(line.search(/:/i));
		  var kq3=isChordLine(line);
          //if (kq < 1 || kq2 > 0)
		  if (isChordLine(line))
		 	 output.push("<span>" + wrapChords(line) + "</span>");
			 //output.push("<span>" + kq2 + line + "</span>");
          else 
			 //output.push("<span>" + wrapChords(line) + "</span>");
			 output.push("<span>" + line + "</span>");
      };

      $(this).html(output.join("\n"));
    });
  };


  $.fn.transpose.defaults = {
    chordRegex: /^[A-G][b\#]?(2|5|6|7|9|11|13|6\/9|7\-5|7\-9|7\#5|7\#9|7\+5|7\+9|7b5|7b9|7sus2|7sus4|sus4|\+|7add11|	|add2|add4|add9|add11|no5|aug|dim|dim7|m\/maj7|m6|m7|m7b5|m9|m11|m13|maj7|maj9|maj11|maj13|mb5|m|sus|sus2|sus4)*(\/[A-G][b\#]*)*?(.\d)*$/,
    chordReplaceRegex: /([A-G][b\#]?(2|5|6|7|9|11|13|6\/9|7\-5|7\-9|7\#5|7\#9|7\+5|7\+9|7b5|7b9|7sus2|7sus4|sus4|\+|7add11|	|add2|add4|add9|add11|no5|aug|dim|dim7|m\/maj7|m6|m7|m7b5|m9|m11|m13|maj7|maj9|maj11|maj13|mb5|m|sus|sus2|sus4)*(\/[A-G][b\#]*)*)?(.\d)*/g
  };

})(jQuery);