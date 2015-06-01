
// Load Chords from XML


(function($){
	
    $.fn.chordsLoad = function(chordsList) {
		
		objArrChords = {};	
		$(chordsList).each(function() {
			$.ajax({
				type: "GET",
				url:  "/Data/chords/data/"+escape(this)+".xml",
				dataType: "xml",
				success: function(xml) {
					var chordName = $(xml).find("Name").text();
					objTmpChord = {name: chordName };
					var $arrApps = $(xml).find("App");
					//alert($arrApps.length);
					var $ID = 0;
					$arrApps.each(
							function(){
								//alert($(this).children("Fr").text());
								$ID ++;
								var fr = parseInt($(this).children("Fret").text());
								var capoFr = parseInt($(this).children("Capo").children("Fr").text());
								var capoLength = parseInt($(this).children("Capo").children("Length").text());
								var f1Str = parseInt($(this).children("F1").children("Str").text());
								var f1Pos = parseInt($(this).children("F1").children("Pos").text());
								var f1Length = parseInt($(this).children("F1").children("Length").text());
								
								var f2Str = parseInt($(this).children("F2").children("Str").text());
								var f2Pos = parseInt($(this).children("F2").children("Pos").text());
								var f2Length = parseInt($(this).children("F2").children("Length").text());
								
								var f3Str = parseInt($(this).children("F3").children("Str").text());
								var f3Pos = parseInt($(this).children("F3").children("Pos").text());
								var f3Length = parseInt($(this).children("F3").children("Length").text());
								
								var f4Str = parseInt($(this).children("F4").children("Str").text());
								var f4Pos = parseInt($(this).children("F4").children("Pos").text());
								var f4Length = parseInt($(this).children("F4").children("Length").text());
								
								var s1 =  parseInt($(this).children("S1").text());
								var s2 =  parseInt($(this).children("S2").text());
								var s3 =  parseInt($(this).children("S3").text());
								var s4 =  parseInt($(this).children("S4").text());
								var s5 =  parseInt($(this).children("S5").text());
								var s6 =  parseInt($(this).children("S6").text());
								
								var tmpApp = {
									capo:[capoFr,capoLength],
									fr:fr,
									f1:[f1Str, f1Pos, f1Length] ,
									f2:[f2Str, f2Pos, f1Length],
									f3:[f3Str, f3Pos, f1Length],
									f4:[f4Str, f4Pos, f1Length],
									s1: s1,
									s2: s2,
									s3: s3,
									s4: s4,
									s5: s5,
									s6: s6
								}
								objTmpChord["app_"+$ID] = tmpApp;									
						})
	
					objArrChords[chordName] = objTmpChord;
				}
			});
		});
		$("span .c").each(function(){
			$(this).hover(function(){				
				$this = $(this);
				//$("#chordPanel").css("left") = $this.css("left");
				offset = $this.offset();
				//offset = $this.position();
				//$("#chordPanel").offset({ top: offset.top, left: offset.left + 150})
				
				//$("#chordPanel").css("top") = $this.css("top");
				//$("#chordPanel").stop(true,true).show();	
				$("#chordPanel").stop(true,true).show();	
				$("#chordPanel").offset({ top: Math.round(offset.top - 270), left: Math.round(offset.left)})
				//set chord accordingly		
				id = 1;
				$val = $this.text() + $(this).attr("title");
				chordApp = $val.split('.');	
				if (chordApp[1] == null ) 
				{
					id = 1;
				}
				else
				{
					id = parseInt(chordApp[1]);
				}
				chord = chordApp[0];
				//chord = chord.replace("/","Slash");
				$(window).chordsDisplay(objArrChords[chord],id);	
				},
				function(){
				$("#chordPanel").fadeOut(500);		
				});
			});
    };
}(jQuery));

//Chorddisplay

(function($){
    $.fn.chordsDisplay = function(objChord, iVariation, ptPosition) {
		
		iVarLength = -1;	
		romanNum = ["0", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII", "XIII", "XIV", "XV", "XVI", "XVII", "XVIII", "XIX", "XX", "XXI", "XXII", "XXIII" ];
		// Check if fretboard exists, if yes change it, otherwise create
		if ($("#chordPanel").length == 0)
		{
			
			
			//Initiliazation

			$chordPanel = $('<div id="chordPanel"></div>');
			$fretBoard = $('<div id="fretBoard" class="fretBoard"></div>');
			$fretPanel = $('<div id="fretPanel" class="fretPanel"></div>');		
			$chordFrNumber=$('<div id="chordFrNumber" class="chordFont chordFr chordFrNumber">I</div>');
			$chordCapo=$('<div id="chordCapo" class="chordCapo"></div>');
			$chordStringPanel=$('<div id="chordStringPanel" class="chordFont chordStringPanel"></div>');
			$chordString6=$('<div id="chordString6" class="chordFont chordString chordString6">E</div>');
			$chordString5=$('<div id="chordString5" class="chordFont chordString chordString5">A</div>');
			$chordString4=$('<div id="chordString4" class="chordFont chordString chordString4">D</div>');
			$chordString3=$('<div id="chordString3" class="chordFont chordString chordString3">G</div>');
			$chordString2=$('<div id="chordString2" class="chordFont chordString chordString2">B</div>');
			$chordString1=$('<div id="chordString1" class="chordFont chordString chordString1">E</div>');
			$chordFinger4=$('<div id="chordFinger4" class="chordString chordString4 chordStringFinger4"></div>');
			$chordFinger3=$('<div id="chordFinger3" class="chordString chordString3 chordStringFinger3"></div>');
			$chordFinger2=$('<div id="chordFinger2" class="chordString chordString2 chordStringFinger2"></div>');
			$chordFinger1=$('<div id="chordFinger1" class="chordString chordString1 chordStringFinger1"></div>');
			$chordFooterPanel=$('<div id="chordFooterPanel" class="chordFooterPanel"></div>');
			$chordFooter=$('<div id="chordFooter" class="chordFont chordFooter chordFooterName"></div>');
			$chordFooterLeft=$('<div id="chordFooterLeft" class="chordFont chordFooter chordFooterLeft"><a id="arrowLeft" href="javascript: void(0)" class="arrow"><img src="/Data/chords/1x1.gif" width="1" height="1"></a></div>');
			$chordFooterRight=$('<div id="chordFooterRight" class="chordFont chordFooter chordFooterRight"><a id="arrowRight" href="javascript: void(0)" class="arrow"><img src="/Data/chords/1x1.gif" width="1" height="1"></a></div>');
			$chordFooterChord=$('<div id="chordFooterChord" class="chordFont chordFooter chordFooterChord">C</div>');
			$chordSignPanel=$('<div id="chordSignPanel" class="chordFont chordSignPanel"></div>');
			$chordStringSign6=$('<div id="chordStringSign6" class="chordString chordString6 chordStringSign chordStringSign1"></div>');
			$chordStringSign5=$('<div id="chordStringSign5" class="chordString chordString5 chordStringSign chordStringSign1"></div>');
			$chordStringSign4=$('<div id="chordStringSign4" class="chordString chordString4 chordStringSign chordStringSign1"></div>');
			$chordStringSign3=$('<div id="chordStringSign3" class="chordString chordString3 chordStringSign chordStringSign1"></div>');
			$chordStringSign2=$('<div id="chordStringSign2" class="chordString chordString2 chordStringSign chordStringSign2"></div>');
			$chordStringSign1=$('<div id="chordStringSign1" class="chordString chordString1 chordStringSign chordStringSign3"></div>');
			$('body').append($chordPanel);	
			$("#chordPanel").append($fretPanel);
			$("#chordPanel").append($chordStringPanel);			
			$("#chordPanel").append($chordSignPanel);
			$("#chordPanel").append($chordFooterPanel);	
			$("#chordSignPanel").append($chordStringSign6);
			$("#chordSignPanel").append($chordStringSign5);
			$("#chordSignPanel").append($chordStringSign4);
			$("#chordSignPanel").append($chordStringSign3);
			$("#chordSignPanel").append($chordStringSign2);
			$("#chordSignPanel").append($chordStringSign1);
			$("#chordFooterPanel").append($chordFooter);
			$("#chordFooterPanel").append($chordFooterLeft);
			$("#chordFooterPanel").append($chordFooterRight);
			$("#chordFooterPanel").append($chordFooterChord);
			$("#chordStringPanel").append($chordString6);
			$("#chordStringPanel").append($chordString5);
			$("#chordStringPanel").append($chordString4);
			$("#chordStringPanel").append($chordString3);
			$("#chordStringPanel").append($chordString2);
			$("#chordStringPanel").append($chordString1);
			$("#fretPanel").append($fretBoard);		
			$("#fretBoard").append($chordCapo);	
			$("#fretBoard").append($chordFrNumber);				
			$("#fretBoard").append($chordFinger4);
			$("#fretBoard").append($chordFinger3);
			$("#fretBoard").append($chordFinger2);
			$("#fretBoard").append($chordFinger1);			
	
			

		
		
			//Bind event
			$this = $("#chordPanel");
			$this.draggable();
			$this.hover(
				function(){					
				$("#chordPanel").stop(true,true).show();				
				},
				function(){
				$("#chordPanel").fadeOut(500);				
				}
				);

		}
		//Set Hop Am
		$chordFinger4.hide();
		$chordFinger3.hide();
		$chordFinger2.hide();
		$chordFinger1.hide();
		$chordCapo.hide();
		$chordStringSign6.hide();
		$chordStringSign5.hide();
		$chordStringSign4.hide();						
		$chordStringSign3.hide();
		$chordStringSign2.hide();
		$chordStringSign1.hide();		
		//Fetch data
		//get Variation Length
		$.each(objChord, function(iAppIndex, objAppValue) { 
			iVarLength++;
		});					
		//check ivarLength against variation
		if (iVariation == 0) iVariation = iVarLength;
		if (iVariation == iVarLength+1) iVariation = 1;		
		$.each(objChord, function(iAppIndex, objAppValue) { 
			//skip name field and check for applicature field
				if (iAppIndex.indexOf("app") >= 0) {
					//check current variation
					iCurVariation = iAppIndex.substring(4);
					
					//if matched then loop through each fields
					if (iCurVariation == iVariation)
					{
					
						iFr = objAppValue.fr;
						if (objAppValue.capo[0] != 0)
						{
										
						}
						
						if (objAppValue.f1[0] !=0) 
						{
							//set string
							$chordFinger1.css("top",3 + (objAppValue.f1[0]-1)*25.6);
							//set pos							
							$chordFinger1.css("left",18 + (objAppValue.f1[1]-1)*47);
							
							$chordFinger1.show();
							if (objAppValue.f1[2] !=0) 
							{
								$chordCapo.css("left",19 + (objAppValue.f1[1]-1)*47);
								$chordCapo.css("height",50 + (objAppValue.f1[2]-2)*25);			
								$chordCapo.show();	
							}
						}
						if (objAppValue.f2[0] !=0) 
						{
							//set string
							$chordFinger2.css("top",3 + (objAppValue.f2[0]-1)*25.6);
							//set pos							
							$chordFinger2.css("left",18 + (objAppValue.f2[1]-1)*47);
							$chordFinger2.show();
						}
						if (objAppValue.f3[0] !=0) 
						{
							//set string
							$chordFinger3.css("top",3 + (objAppValue.f3[0]-1)*25.6);
							//set pos							
							$chordFinger3.css("left",18 + (objAppValue.f3[1]-1)*47);
							$chordFinger3.show();
						}
						if (objAppValue.f4[0] !=0) 
						{
							//set string
							$chordFinger4.css("top",3 + (objAppValue.f4[0]-1)*25.6);
							//set pos							
							$chordFinger4.css("left",18 + (objAppValue.f4[1]-1)*47);
							$chordFinger4.show();
						}
						
						switch (objAppValue.s1)
						{
							case 0 : $chordStringSign1.css( "background-image", "url(/Data/chords/o.png)" ); break;
							case -1: $chordStringSign1.css( "background-image", "url(/Data/chords/x.png)" ); break;
							case 1 : $chordStringSign1.css( "background-image", "url(/Data/chords/oo.png)" ); break;
						}
						switch (objAppValue.s2)
						{
							case 0 : $chordStringSign2.css( "background-image", "url(/Data/chords/o.png)" ); break;
							case -1: $chordStringSign2.css( "background-image", "url(/Data/chords/x.png)" ); break;
							case 1 : $chordStringSign2.css( "background-image", "url(/Data/chords/oo.png)" ); break;
						}
						switch (objAppValue.s3)
						{
							case 0 : $chordStringSign3.css( "background-image", "url(/Data/chords/o.png)" ); break;
							case -1: $chordStringSign3.css( "background-image", "url(/Data/chords/x.png)" ); break;
							case 1 : $chordStringSign3.css( "background-image", "url(/Data/chords/oo.png)" ); break;
						}
						switch (objAppValue.s4) 
						{
							case 0 : $chordStringSign4.css( "background-image", "url(/Data/chords/o.png)" ); break;
							case -1: $chordStringSign4.css( "background-image", "url(/Data/chords/x.png)" ); break;
							case 1 : $chordStringSign4.css( "background-image", "url(/Data/chords/oo.png)" ); break;
						}
						switch (objAppValue.s5)
						{
							case 0 : $chordStringSign5.css( "background-image", "url(/Data/chords/o.png)" ); break;
							case -1: $chordStringSign5.css( "background-image", "url(/Data/chords/x.png)" ); break;
							case 1 : $chordStringSign5.css( "background-image", "url(/Data/chords/oo.png)" ); break;
						}
						switch (objAppValue.s6) 
						{
							case 0 : $chordStringSign6.css( "background-image", "url(/Data/chords/o.png)" ); break;
							case -1: $chordStringSign6.css( "background-image", "url(/Data/chords/x.png)" ); break;
							case 1 : $chordStringSign6.css( "background-image", "url(/Data/chords/oo.png)" ); break;
						}

					}
				}
			});
		
		//Set arrow action
		
		//unbind previous handle
		$("#arrowLeft").unbind('click');
		$("#arrowRight").unbind('click');		
		//set click event
		$("#arrowLeft").click(function(){
			$(window).chordsDisplay(objChord,iVariation-1,ptPosition);
			});
		$("#arrowRight").click(function(){
			$(window).chordsDisplay(objChord,iVariation+1,ptPosition);
			});
		//Display data
			
			$chordFooterChord.text(objChord.name);
			$chordFrNumber.text(romanNum[iFr]);
			$chordFooter.text('('+iVariation+')');
			$chordStringSign6.show();
			$chordStringSign5.show();
			$chordStringSign4.show();						
			$chordStringSign3.show();
			$chordStringSign2.show();
			$chordStringSign1.show();	

			
			
    };
}(jQuery));