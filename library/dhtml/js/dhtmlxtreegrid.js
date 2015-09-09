
/**
*   @desc: switch current row state (collapse/expand) tree grid row
*   @param: obj - row object
*   @type: private
*/
dhtmlXGridObject.prototype._updateTGRState=function(z){ 
	if (!z.update || z.id==0) return;
	this.rowsAr[z.id].imgTag.src=this.imgURL+z.state+".gif";
	z.update=false;
}


dhtmlXGridObject.prototype.doExpand=function(obj){  
	this.editStop();
    var row = obj.parentNode.parentNode.parentNode;
	var r=this._h2.get[row.idd];
	if (!this.callEvent("onOpen",[row.idd,(r.state=="plus"?-1:1)])) return;
    if(r.state=="plus")
      this.expandKids(row)
    else
   	  if((r.state=="minus")&&(!r._closeable))
          this.collapseKids(row)
          
	
}


function dhtmlxHierarchy(){
		
		var z={id:0, childs:[], level:-1, parent:null, index:0, state:dhtmlXGridObject._emptyLineImg};
		this.order=[z];
		this.get={"0":z};

		this.swap=function(a,b){
			var p=a.parent;
			var z=a.index;
			p.childs[z]=b;
			p.childs[b.index]=a;
			a.index=b.index; b.index=z;
		}
		this.forEachChild=function(id,funct,that){
				var z=this.get[id];
				for (var i=0; i<z.childs.length; i++){
					funct.apply((that||this),[z.childs[i]]);
					if (z.childs[i].childs.length) this.forEachChild(z.childs[i].id,funct,that);
				}
		}
		this.change=function(id,name,val){
			var z=this.get[id];
			if (z[name]==val) return;
				z[name]=val;
				z.update=true;
		}
		this.add=function(id,parentId){ 
			return this.addAfter(id,parentId);
		}
		this.addAfter=function(id,parentId,afterId,fix){  
			var z=this.get[parentId||0];
			if (afterId)
				var ind=this.get[afterId].index+(fix?0:1);
			else var ind=z.childs.length;
			
			var x={id:id, childs:[], level:z.level+1, parent:z, index:ind, state:dhtmlXGridObject._emptyLineImg}
			if (z.childs.length==0)  this.change(parentId,"state",(parentId==0?"minus":"plus"));
			
			if (afterId){
				for (var i=ind; i<z.childs.length; i++) z.childs[i].index++;
				z.childs=z.childs.slice(0,ind).concat([x]).concat(z.childs.slice(ind,z.childs.length));
			}else
				z.childs.push(x);
			this.get[id]=x;
			return x;
		}
		this.addBefore=function(id,parentId,beforeId){
			return this.addAfter(id,parentId,beforeId,true)
		}		
		this.remove=function(id){  
			var z=this.get[id||0];
			for (var i=0; i<z.childs.length; i++)
				this.deleteAll(z.childs[i].id)
			z.childs=[];	
			z.parent.childs=z.parent.childs.slice(0,z.index).concat(z.parent.childs.slice(z.index+1));				
			for (var i=z.index; i<z.parent.childs.length; i++)
				z.parent.childs[i].index--;
			delete this.get[id];
		}
		this.deleteAll=function(id){
			var z=this.get[id||0];
			for (var i=0; i<z.childs.length; i++)
				this.deleteAll(z.childs[i].id)
				
			z.childs=[];				
			delete this.get[id];
		}		
		
		return this;
	}

dhtmlXGridObject.prototype._getOpenLenght=function(id,start){
	
	var z=this._h2.get[id].childs;
	start+=z.length;
	for (var i=0; i<z.length; i++)
		if (z[i].childs.length && z[i].state=='minus')
			start+=this._getOpenLenght(z[i].id,0);
	return start;
}
/**
*   @desc: close row of treegrid (removes kids from dom)
*   @param: curRow - row to process kids of
*   @type: private
*/
dhtmlXGridObject.prototype.collapseKids=function(curRow){ 
	var r=this._h2.get[curRow.idd];
    if (r.state!="minus") return;
    if (!this.callEvent("onOpenStart",[curRow.idd,1])) return;

    var start = curRow.rowIndex;
    //why Safari doesn't support standards?
    if (start<0) start=this.rowsCol._dhx_find(curRow)+1;

   	this._h2.change(r.id,"state","plus");
   	this._updateTGRState(r);

    var len=this._getOpenLenght(this.rowsCol[start-1].idd,0);
    for (var i=0; i<len; i++)
    	this.rowsCol[start+i].parentNode.removeChild(this.rowsCol[start+i]);
    this.rowsCol.splice(start,len);

    //if (this._cssEven && !this._cssSP)
    this.callEvent("onGridReconstructed",[]);

    this.callEvent("onOpenEnd",[curRow.idd,-1]);
    this.setSizes();
}



dhtmlXGridObject.prototype._massInsert=function(r,start,ind){
	var anew=[];
	var par=(_isKHTML?this.obj:this.obj.rows[0].parentNode)
	for(var i=0;i<r.childs.length;i++){
		var ra=this.rowsAr[r.childs[i].id];
		if (start)
			start.parentNode.insertBefore(ra,start);
		else
			par.appendChild(ra);
		anew.push(ra)
		}
		
	this.rowsCol=dhtmlXHeir(this.rowsCol.slice(0,ind).concat(anew).concat(this.rowsCol.slice(ind)),this._aEx);
	
	
	var dx=0;
	for(var i=0;i<r.childs.length;i++)
		if (r.childs[i].state=="minus")
			dx+=this._massInsert(r.childs[i],this.rowsCol[ind+i+dx+1],ind+i+dx+1);
			
	return r.childs.length+dx;
}
/**
*   @desc: change parent of row, correct kids collections
*   @param: curRow - row to process
*   @type: private
*/
dhtmlXGridObject.prototype.expandKids=function(curRow,sEv){ 

	var r=this._h2.get[curRow.idd];
	if ((!r.childs.length)&&(!r._xml_await)) return;
	if (r.state!="plus") return;
    
    
    if (!r._loading && !sEv)
    	if (!this.callEvent("onOpenStart",[r.id,-1])) return;
        


   var start = this.getRowIndex(r.id)+1;
   if(r.childs.length){
        r._loading=false;
        this._h2.change(r.id,"state","minus")
        this._updateTGRState(r);
		var len=this._massInsert(r,this.rowsCol[start],start);
		
		//if (this._cssEven && !this._cssSP)
		this.callEvent("onGridReconstructed",[]);
			

   }else{	
        if (r._xml_await){
         if ((this._slowParse)&&(curRow._xml)){
            this._reParse(curRow);
            return this.expandKids(curRow,true);
         }
         if(this.kidsXmlFile.indexOf("?")!=-1)
            var s = "&";
         else
            var s = "?";
           r._loading=true;
         this.loadXML(this.kidsXmlFile+""+s+"id="+r.id);
        }
   }
    this.setSizes();
    if (!r._loading)
    this.callEvent("onOpenEnd",[r.id,1]);
}

dhtmlXGridObject.prototype.kidsXmlFile = "";



/**
*   @desc: sorts treegrid by specified column
*   @param: col - column index
*   @param:   type - str.int.date
*   @param: order - asc.desc
*   @type: public
*   @edition: Professional
*   @topic: 2,3,5,9
*/
dhtmlXGridObject.prototype.sortTreeRows = function(col,type,order){
	            this.forEachRow(function(id){
                	var z=this._h2.get[id];
                	var label=this.cells(id,col).getValue();
                	if(type=='int'){
						   z._sort=parseFloat(label);
						   z._sort=isNaN(z._sort)?-99999999999999:z._sort;
                     }else
                        z._sort=label;
                	});
                	
				var self=this;
				var pos=1; var neg=-1;
				if (order=="des") { pos=-1; neg=1; }
					
				var funct=null;
	                if(type=='cus')
    	                 funct=function(a,b){
                            return self._customSorts[col](a._sort,b._sort,order);
                                    };
     	
                   if(type=='str')
                     funct=function(a,b){return (a._sort<b._sort?neg:(a._sort==b._sort?0:pos))}

                  if(type=='int')
                     funct=function(a,b){return (a._sort<b._sort?neg:(a._sort==b._sort?0:pos))}

                  if(type=='date')
                     funct=function(a,b){return (Date.parse(new Date(a._sort))-Date.parse(new Date(b._sort)))*pos}
                  
                  this._sortTreeRows(funct,0);
                  for (var i=0; i<this.rowsCol.length; i++)
                  	this.rowsCol[i].parentNode.removeChild(this.rowsCol[i]);
                  this.rowsCol=new dhtmlxArray();
                  this._renderSort(0,true);

            this.callEvent("onGridReconstructed",[]);
               
}

dhtmlXGridObject.prototype._sortTreeRows = function(funct,id){
				var ar=this._h2.get[id].childs;
				ar.sort(funct);
				for (var i=0; i<ar.length; i++)				
					if (ar[i].childs.length) 
						this._sortTreeRows(funct,ar[i].id);
};
dhtmlXGridObject.prototype._renderSort = function(id,mode){ 
				var ar=this._h2.get[id].childs;
				var par=(_isKHTML?this.obj:this.obj.rows[0].parentNode);
				for (var i=0; i<ar.length; i++){
					if (mode){
						var row=this.rowsAr[ar[i].id];
						par.appendChild(row)
						this.rowsCol.push(row);
                	}
					ar[i].index=i;
					if (ar[i].childs.length) 
						if (ar[i].state=="minus") this._renderSort(ar[i].id,true);
					}
};

dhtmlXGridObject.prototype._fixAlterCssTG = function(){ }
dhtmlXGridObject.prototype._fixAlterCssTGR = function(){ 
	this._h2.forEachChild(0,function(x){
		this.rowsAr[x.id].className=((x.level%2)?(this._cssUnEven+" "+this._cssUnEven):(this._cssEven+" "+this._cssEven))+"_"+x.level+(this.rowsAr[x.id]._css||"");
	},this);
}
dhtmlXGridObject.prototype.moveRowUDTG = function(id,dir){ 
	var x=this._h2.get[id];
	var p=x.parent.childs[x.index+dir]
	if ((!p) || (p.parent!=x.parent)) return;
	var state=[x.state,p.state];
	this.collapseKids(this.rowsAr[x.id]);
	this.collapseKids(this.rowsAr[p.id]);	
	var ind = this.rowsCol._dhx_find(this.rowsAr[id]);
	
	var nod=this.obj.rows[0].parentNode.removeChild(this.rowsCol[ind]);	
	var tar=this.rowsCol[ind+((dir==1)?2:dir)];
	if (tar)
		tar.parentNode.insertBefore(nod,tar);
	else
		this.obj.rows[0].parentNode.appendChild(nod);
	this.rowsCol._dhx_swapItems(ind,ind+dir)
	this._h2.swap(p,x);
	
	
	if (state[0]=="minus") this.expandKids(this.rowsAr[x.id]);
	if (state[1]=="minus") this.expandKids(this.rowsAr[p.id]);	
}

/**
*   @desc: TreeGrid cell constructor (only for TreeGrid package)
*   @param: cell - cell object
*   @type: public
*/
function eXcell_tree(cell){
   if (cell){
      this.cell = cell;
      this.grid = this.cell.parentNode.grid;
   }
   this.isDisabled = function(){ return this.grid._edtc; }
   this.edit = function(){
        if ((this.er)||(this.grid._edtc)) return;
        this.er=this.cell.parentNode.valTag;
        this.val=this.er.innerHTML;
        this.er.innerHTML="<textarea class='dhx_combo_edit' type='text' style='height:"+(this.cell.offsetHeight-6)+"px; width:100%; border:0px; margin:0px; padding:0px; padding-top:"+(_isFF?1:2)+"px; overflow:hidden;'></textarea>";
        this.er.childNodes[0].onmousedown = function(e){(e||event).cancelBubble = true}
        this.er.childNodes[0].onselectstart=function(e){  if (!e) e=event; e.cancelBubble=true; return true;  };
        if (_isFF)         this.er.style.top="1px";
        this.er.className+=" editable";
        this.er.firstChild.onclick = function(e){(e||event).cancelBubble = true};
        this.er.firstChild.value=this.val;
        this.er.firstChild.focus();
    }
   this.detach = function(){
        if (!this.er) return;
            this.er.innerHTML=this.er.firstChild.value;
            this.er.className=this.er.className.replace("editable","");
            var z=(this.val!=this.er.innerHMTL);
            if (_isFF) this.er.style.top="2px";
			if (this.grid._onCCH)
				this.grid._onCCH(this.cell.parentNode.idd,this.cell._cellIndex,this.er.innerHTML);
            this.er=null;
        return (z);
    }
   this.getValue = function(){
   		return this.getLabel();
   }

    
    /**
    *   @desc: get image of treegrid item
    *   @param: content - new text of label
    *   @type: private
    */
   this.setImage = function(url){
        this.cell.parentNode.imgTag.nextSibling.src=this.grid.imgURL+"/"+url;
        this.grid._h2.get[this.cell.parentNode.idd].image=url;
   }
    /**
    *   @desc: set image of treegrid item
    *   @param: content - new text of label
    *   @type: private
    */
   this.getImage = function(){
   		this.grid._h2.get[this.cell.parentNode.idd].image;
   }

   /**
   *   @desc: sets text representation of cell ( setLabel doesn't triger math calculations as setValue do)
   *   @param: val - new value
   *   @type: public
   */
   this.setLabel = function(val){
                  this.setValueA(val);
            }

   /**
   *   @desc: sets text representation of cell ( setLabel doesn't triger math calculations as setValue do)
   *   @param: val - new value
   *   @type: public
   */
   this.getLabel = function(val){
     return this.cell.parentNode.valTag.innerHTML;
    }
}
    /**
    *   @desc: set value of grid item
    *   @param: val  - new value (for treegrid this method only used while adding new rows)
    *   @type: private
    */
	
eXcell_tree.prototype = new eXcell;
    /**
    *   @desc: set label of treegrid item
    *   @param: content - new text of label
    *   @type: private
    */
   eXcell_tree.prototype.setValueA = function(content){
 		this.cell.parentNode.valTag.innerHTML=content;
		if (this.grid._onCCH)
			this.grid._onCCH(this.cell.parentNode.idd,this.cell._cellIndex,content);
    }
	eXcell_tree.prototype.setValue = function(valAr){
		if (typeof(valAr)!="object")
			valAr = valAr.split("^");//parent_id^Label^children^im0^im1^im2
			
		if (valAr.length==1)
			return this.setLabel(valAr[0]);
			
		if ((this.grid._tgc.imgURL==null)||(this.grid._tgc.imgURL!=this.grid.imgURL)){
			var _tgc={};
			_tgc.spacer="<img src='"+this.grid.imgURL+"blanc.gif'  align='absmiddle' class='space'>";
			_tgc.imst="<img src='"+this.grid.imgURL;
			_tgc.imact="' align='absmiddle'  onclick='this."+(_isKHTML?"":"parentNode.")+"parentNode.parentNode.parentNode.parentNode.grid.doExpand(this);event.cancelBubble=true;'>"
			_tgc.plus=_tgc.imst+"plus.gif"+_tgc.imact;
			_tgc.minus=_tgc.imst+"minus.gif"+_tgc.imact;
			_tgc.blank=_tgc.imst+"blank.gif"+_tgc.imact;
			_tgc.start="<div style=' overflow:hidden; white-space : nowrap; height:"+(_isIE?20:18)+"px;'>";
			
			_tgc.itemim="' align='absmiddle' "+(this.grid._img_height?(" height=\""+this.grid._img_height+"\""):"")+(this.grid._img_width?(" width=\""+this.grid._img_width+"\""):"")+" ><span "+(_isFF?"style='position:relative; top:2px;'":"")+"id='nodeval'>";
			_tgc.close="</span></div>";
			this.grid._tgc=_tgc;
		}
		var _h2=this.grid._h2;
		var _tgc=this.grid._tgc;
				
		var rid=this.cell.parentNode.idd;
		var pid=valAr[0];
		
		if (_h2.get[rid])   return dhtmlxError.throwError("DuplicateID","Not unique ID :: "+rid+", row skiped",[rid]); 
		
		
		var prow=_h2.get[(pid||0)];
		if ((this.grid._add_trgr || this.grid._add_trgr=="0") && prow.childs.length){
			if (this.grid._add_trgr==0)
				var row =_h2.addBefore(rid,(pid||0),prow.childs[0].id);
			else
				var row =_h2.addAfter(rid,(pid||0),prow.childs[this.grid._add_trgr-1].id);
			delete this.grid._add_trgr;
		}
		else
			var row =_h2.add(rid,(pid||0))
		
		
		if ((!this.grid.kidsXmlFile)&&(!this.grid._slowParse)) valAr[2]=0;
		
		row.has_kids=(valAr[2]||0);
		row._xml_await=(valAr[2]!=0);
		row.image=valAr[3];
		row.label=valAr[1];
               
        var html=[_tgc.start];
		
        for(var i=0;i<row.level;i++)
        	html.push(_tgc.spacer);
        
       //if has children
        if(valAr[2]!="" && valAr[2]!=0){
        	html.push(_tgc.plus);
        	row.state="plus"
        	}
        else
        	html.push(_tgc.blank);
                        
		html.push(_tgc.imst);
		html.push(valAr[3]);
		html.push(_tgc.itemim);
		html.push(valAr[1]);
		html.push(_tgc.close);
		
                    

		this.cell.innerHTML=html.join("");
		this.cell.parentNode.imgTag=this.cell.childNodes[0].childNodes[row.level];
		this.cell.parentNode.valTag=this.cell.childNodes[0].childNodes[row.level+2];
		if (_isKHTML) this.cell.vAlign="top";
		if (prow.id!=0 && prow.state=="plus") {
				this.grid._updateTGRState(prow,false);
				this.cell.parentNode._skipInsert=true;		
			}

		this.grid.callEvent("onCellChanged",[rid,this.cell._cellIndex,valAr[1]]);
	}
    
    /**
    *   @desc: remove row from treegrid
    *   @param: node  - row object
    *   @type: private
    */
dhtmlXGridObject.prototype._removeTrGrRow=function(node,x){
		 if(x){
		     this._h2.forEachChild(x.id,function(x){
		     	this._removeTrGrRow(null,x);
	    		delete this.rowsAr[x.id];
    		},this);
    		return;
		 }
		 
		 var ind=this.getRowIndex(node.idd);
		 var x=this._h2.get[node.idd];
		 
		 if (ind!=-1){
		 	var len=1;
		 	if (x && x.state=="minus") len+=this._getOpenLenght(x.id,0)
		 	for (var i=0; i<len; i++)
            	this.rowsCol[i+ind].parentNode.removeChild(this.rowsCol[i+ind]);
            	
	         this.rowsCol.splice(ind,len);
	    }
	    
	    if (!x) return;
		this._removeTrGrRow(null,x);
    	delete this.rowsAr[x.id];
	
    	if (x.parent.childs.length==1){
    		this._h2.change(x.parent.id,"state",dhtmlXGridObject._emptyLineImg);
    		this._updateTGRState(x.parent);
    	}
		this._h2.remove(x.id);
		if (this._math_summ && x.parent)
        	this._recalc_summ(x.parent);
      }




/**
*   @desc: expand row
*   @param: rowId - id of row
*   @type:  public
*   @edition: Professional
*   @topic: 7
*/
dhtmlXGridObject.prototype.openItem=function(rowId){
		var y=this._h2.get[rowId||0];
        var x=this.getRowById(rowId||0);
		if (!x) return;
        if (y.parent && y.parent.id!=0)
        	this.openItem(y.parent.id);
        this.expandKids(x);
}

dhtmlXGridObject.prototype._addRowClassic=dhtmlXGridObject.prototype.addRow;

    /**
    *   @desc: add new row to treeGrid
    *   @param: new_id  - new row id
    *   @param: text  - array of row label
    *   @param: ind  - position of row (set to null, for using parentId)
    *   @param: parent_id  - id of parent row
    *   @param: img  - img url for new row
    *   @param: child - child flag [optional]
    *   @type: public
    *   @edition: Professional
    */
dhtmlXGridObject.prototype.addRow=function(new_id,text,ind,parent_id,img,child){ 
	parent_id=parent_id||0;
	var trcol=this.cellType._dhx_find("tree");
    if (typeof(text)=="string") text=text.split(this.delim);
     var last_row=null;
     if ((trcol!=-1)&&((text[trcol]||"").toString().search(/\^/gi)==-1)){
        var def=text[trcol];
        var d=parent_id.toString().split(",");
        for (var i=0; i<d.length; i++){
            text[trcol]=d[i]+"^"+def+"^"+(child?1:0)+"^"+(img||"leaf.gif");
                if (d[i]!=0)
                if ((!ind)||(ind==0)){
                    ind=this.getRowIndex(d[i]);
                  if (ind!=-1){
                  		if (this._h2.get[d[i]].state=="minus") ind+=this._getOpenLenght(d[i],0)+1;
                  		if ((this._slowParse)&&(this.rowsCol[ind]._xml)) this._reParse(this.rowsCol[ind]);
              		}
                }
            if(!this._add_trgr){
            if (this._h2.get[parent_id].state!="minus") this._add_trgr=this._h2.get[parent_id].childs.length;
            else
            if(this.rowsCol[ind-1])
            	this._add_trgr=this.trackParent(this._h2.get[this.rowsCol[ind-1].idd],(parent_id||0))
            else this._add_trgr=0; }
            
            last_row=this._addRowClassic(new_id,text,((!parent_id)&&(!ind)&&(ind!="0"))?window.undefined:ind);
            }
        return last_row;
     }
     return this._addRowClassic(new_id,text,ind);
}
dhtmlXGridObject.prototype.trackParent=function(x,id){ 
	if (x.id==id) return 0;
	if (x.parent.id==id) return x.index+1;
	
	return this.trackParent(x.parent,id);
}
dhtmlXGridObject.prototype.addRowBefore=function(new_id,text,sibl_id,img,child){
	var sb=this.rowsAr[sibl_id];
	if (!sb) return;

	var ind=this.getRowIndex(sibl_id);
	this._add_trgr=this._h2.get[sibl_id].index;
	return this.addRow(new_id,text,ind,this._h2.get[sibl_id].parent.id,img,child);
}
dhtmlXGridObject.prototype.addRowAfter=function(new_id,text,sibl_id,img,child){
	var sb=this.rowsAr[sibl_id];
	if (!sb) return;
	var ind=this.getRowIndex(sibl_id);
	this._add_trgr=this._h2.get[sibl_id].index+1;
	if (this._h2.get[sibl_id].state=="minus") ind+=this._getOpenLenght(sibl_id,0)+1;	
	else	ind++;
	return this.addRow(new_id,text,ind,this._h2.get[sibl_id].parent.id,img,child);
}




//#__pro_feature:21092006{

//#smart_parsing:21092006{
/**
*     @desc: enable/disable smart XML parsing mode (usefull for big, well structured XML)
*     @beforeInit: 1
*     @param: mode - 1 - on, 0 - off;
*     @type: public
*     @edition: Professional
*     @topic: 0
*/
dhtmlXGridObject.prototype.enableSmartXMLParsing=function(mode) { this._slowParse=convertStringToBoolean(mode); };


/**
*     @desc: search id in unparsed chunks and render its node
*     @param: id - id in question
*     @type: prlvate
*     @edition: Professional
*     @topic: 0
*/
dhtmlXGridObject.prototype._seekAndDeploy=function(id) {
   if (this._parsing_) return null;
   if ((id=="null")||(!id)) return;
   var a;
	
   for (a in this.rowsAr)
      if ((this.rowsAr[a])&&(this.rowsAr[a]._xml)){
         var res=this.xmlLoader.doXPath("//row[@id=\""+id+"\"]",this.rowsAr[a]._xml[0].parentNode);
         if (res && res.length){
            //detect back line of ids
            res=res[0];
            var line=new Array();
            while (!this.rowsAr[res.getAttribute("id")]){
               line[line.length]=res.getAttribute("id");
               res=res.parentNode;
            }
            line[line.length]=res.getAttribute("id");

            for (var i=line.length-1; i>0; i--){
               this._reParse(this.rowsAr[line[i]]);
               this._openItem(this.rowsAr[line[i]]);
               }
            for (var i=1; i<line.length; i++)
               this.collapseKids(this.rowsAr[line[i]]);

            return this.getRowById(id);
         }
      }
    return null;
};

/**
*     @desc: reparse branch
*     @param: row - parent row
*     @type: prlvate
*     @edition: Professional
*     @topic: 0
*/
dhtmlXGridObject.prototype._reParse=function(row){
            	var row=this.rowsAr[row.idd];
			if (row._xml){            	
             	var ind=this.rowsCol._dhx_find(row);
            	ind+=this._getOpenLenght(row.idd);
            	this._innerParse(row._xml,ind,this.cellType._dhx_find("tree"),row.idd);
        	}
            row._xml_await=row._xml=null;
}

//#}


    /**
    *   @desc: copy content between different rows
    *   @param: frRow  - source row object
    *   @param: from_row_id  - source row id
    *   @param: to_row_id  - target row id
    *   @type: private
    */
dhtmlXGridObject.prototype._copyTreeGridRowContent=function(frRow,from_row_id,to_row_id){
    var z=this.cellType._dhx_find("tree");
    for(i=0;i<frRow.cells.length;i++){
        if (i!=z)
           this.cells(to_row_id,i).setValue(this.cells(from_row_id,i).getValue())
        else
            this.cells(to_row_id,i).setValueA(this.cells(from_row_id,i).getValue())

    }
}

/**
*   @desc: collapse row
*   @param: rowId - id of row
*   @type:  public
*   @edition: Professional
*   @topic: 7
*/
dhtmlXGridObject.prototype.closeItem=function(rowId){
        var x=this.getRowById(rowId);
        if (!x) return;
        this.collapseKids(x);
}
/**
*   @desc: delete all childs of row in question
*   @param: rowId - id of row
*   @type:  public
*   @edition: Professional
*   @topic: 7
*/
dhtmlXGridObject.prototype.deleteChildItems=function(rowId){
        var z=this._h2.get[rowId];
        if (!z) return;
        while (z.childs.length)
            this.deleteRow(z.childs[0].id);
            
}
/**
*   @desc: get list of id of all nested rows
*   @param: rowId - id of row
*   @type:  public
*   @returns: list of id of all nested rows
*   @edition: Professional
*   @topic: 7
*/
dhtmlXGridObject.prototype.getAllSubItems=function(rowId){
        var str=[];
        var z=this._h2.get[rowId||0];
        if (z)
        for (var i=0; i<z.childs.length; i++){
            str.push(z.childs[i].id);
            if (z.childs[i].childs.length)
            str=str.concat(this.getAllSubItems(z.childs[i].id).split(","));
            }

        return str.join(",");
}

/**
*   @desc: get id of child item at specified position
*   @param: rowId - id of row
*   @param: ind - child node index
*   @type:  public
*   @returns: id of child item at specified position
*   @edition: Professional
*   @topic: 7
*/
dhtmlXGridObject.prototype.getChildItemIdByIndex=function(rowId,ind){
		var z=this._h2.get[rowId||0];
        if (!z) return null;
        return (z.childs[ind]?z.childs[ind].id:null);
}

/**
*   @desc: get real caption of tree col
*   @param: rowId - id of row
*   @type:  public
*   @edition: Professional
*   @returns: real caption of tree col
*   @topic: 7
*/
dhtmlXGridObject.prototype.getItemText=function(rowId){
        var z=this._h2.get[rowId||0];
        if (!z) return "";
        return z.label;
}

/**
*   @desc: return open/close state of row
*   @param: rowId - id of row
*   @type:  public
*   @returns: open/close state of row
*   @edition: Professional
*   @topic: 7
*/
dhtmlXGridObject.prototype.getOpenState=function(rowId){
        var z=this._h2.get[rowId||0];
        if (!z) return;
        if (z.state=="minus") return true;
        return false;
}
/**
*   @desc: return id of parent row
*   @param: rowId - id of row
*   @type:  public
*   @edition: Professional
*   @returns: id of parent row
*   @topic: 7
*/
dhtmlXGridObject.prototype.getParentId=function(rowId){
        var z=this._h2.get[rowId||0];
        if ((!z) || (!z.parent)) return null;
        return z.parent.id;
}
/**
*   @desc: return list of child row id, sparated by comma
*   @param: rowId - id of row
*   @type:  public
*   @edition: Professional
*   @returns: list of child rows
*   @topic: 7
*/
dhtmlXGridObject.prototype.getSubItems=function(rowId){
      var str=[];
      var z=this._h2.get[rowId||0];
      if (z)
      	for (var i=0; i<z.childs.length; i++)
      		str.push(z.childs[i].id);
      return str.join(",");
}


/**
*   @desc: expand all tree structure
*   @type:  public
*   @edition: Professional
*   @topic: 7
*/
dhtmlXGridObject.prototype.expandAll=function(){
	if (this._slowParse) this._deSmartAll();
	for (var i=0; i<this.rowsCol.length; i++)
		this.obj.rows[0].parentNode.removeChild(this.rowsCol[i]);
	this.rowsCol=new dhtmlxArray();
	this._renderAllExpand(0);
	/*
		z=z||this._h2.get[0].childs;
		for (var i=0; i<z.length; i++){
			this.expandKids(this.rowsAr[z[i].id]);
			if(z[i].childs.length) this.expandAll(z[i].childs);
		}
		*/
	this.setSizes();
	this.callEvent("onGridReconstructed",[]);
	//if (!this._cssSP) this._fixAlterCss();
}
dhtmlXGridObject.prototype._deSmartAll=function(){
	for (var i=0; i<this.rowsCol.length; i++)
		if (this.rowsCol[i]._xml){
			this._slowParse=false;
			this._reParse(this.rowsCol[i]);
			this._slowParse=true;
		}
}
	
dhtmlXGridObject.prototype._renderAllExpand=function(z){
	var x=this._h2.get[z].childs;
	for (var i=0; i<x.length; i++){
		var row=this.rowsAr[x[i].id];
		this.rowsCol.push(row);
		this.obj.rows[0].parentNode.appendChild(row);
		if (x[i].childs.length){
			this._h2.change(x[i].id,"state","minus")
			this._updateTGRState(x[i]);
			this._renderAllExpand(x[i].id)
		}
	}
}
/**
*   @desc: collapse all tree structure
*   @type:  public
*   @edition: Professional
*   @topic: 7
*/
dhtmlXGridObject.prototype.collapseAll=function(rowId){
		var z=this._h2.get[0].childs;
		for (var i=0; i<z.length; i++)
			this.collapseKids(this.rowsAr[z[i].id]);
		for (id in this.rowsAr){
			var z=this._h2.get[id];
			if (z && z.state=="minus"){
				z.state="plus";
				z.update=true;
				this._updateTGRState(z);
			}}
}

/**
*   @desc: return children count
*   @param: rowId - id of row
*   @type:  public
*   @edition: Professional
*   @returns: children count
*   @topic: 7
*/
dhtmlXGridObject.prototype.hasChildren=function(rowId){
        var x=this._h2.get[rowId];
        if (x && x.childs.length) return x.childs.length;
        if (this.getRowById(rowId)._xml_await) return -1;
        return 0;
}


/**
*   @desc: enable/disable closing of row
*   @param: rowId - id of row
*   @param: status - true/false
*   @type:  public
*   @edition: Professional
*   @topic: 7
*/

dhtmlXGridObject.prototype.setItemCloseable=function(rowId,status){
        var x=this._h2.get[rowId];
        if (!x) return;
        x._closeable=(!convertStringToBoolean(status));
}
/**
*   @desc: set real caption of tree col
*   @param: rowId - id of row
*   @param: newtext - new text
*   @type:  public
*   @edition: Professional
*   @topic: 7
*/
dhtmlXGridObject.prototype.setItemText=function(rowId,newtext){
	this._h2.get[rowId].label=newtext; 
	this.rowsAr[rowId].valTag.innerHTML=newtext; 
}


/**
*   @desc: set image of tree col
*   @param: rowId - id of row
*   @param: url - image url
*   @type:  public
*   @edition: Professional
*   @topic: 7
*/
dhtmlXGridObject.prototype.setItemImage=function(rowId,url){
	this._h2.get[rowId].image=url; 
	this.rowsAr[rowId].imgTag.nextSibling.src=url; 
}

/**
*   @desc: get image of tree col
*   @param: rowId - id of row
*   @type:  public
*   @edition: Professional
*   @topic: 7
*/
dhtmlXGridObject.prototype.getItemImage=function(rowId){
	return this._h2.get[rowId].image;  
}


/**
*   @desc: set size of treegrid images
*   @param: width -  width of image
*   @param: height - height of image
*   @type:  public
*   @edition: Professional
*   @topic: 7
*/
dhtmlXGridObject.prototype.setImageSize=function(width,height){
        this._img_width=width;
        this._img_height=height;
}


dhtmlXGridObject.prototype._getRowImage=function(row){
	return this._h2.get[row.idd].image;    
        }


/**
*     @desc: set function called before tree node opened/closed
*     @param: func - event handling function
*     @type: public
*     @topic: 0,10
*     @event:  onOpenStart
*     @eventdesc: Event raised immideatly after item in tree got command to open/close , and before item was opened//closed. Event also raised for unclosable nodes and nodes without open/close functionality - in that case result of function will be ignored.
            Event not raised if node opened by dhtmlXtree API.
*     @eventparam: ID of node which will be opened/closed
*     @eventparam: Current open state of tree item. -1 - item closed, 1 - item opened.
*     @eventreturn: true - confirm opening/closing; false - deny opening/closing;
*/
   dhtmlXGridObject.prototype.setOnOpenStartHandler=function(func){  this.attachEvent("onOpenStart",func); };
   
/**
*     @desc: set function called after tree node opened/closed
*     @param: func - event handling function
*     @type: public
*     @topic: 0,10
*     @event:  onOpenEnd
*     @eventdesc: Event raised immideatly after item in tree got command to open/close , and before item was opened//closed. Event also raised for unclosable nodes and nodes without open/close functionality - in that case result of function will be ignored.
            Event not raised if node opened by dhtmlXtree API.
*     @eventparam: ID of node which will be opened/closed
*     @eventparam: Current open state of tree item. -1 - item closed, 1 - item opened.
*/
   dhtmlXGridObject.prototype.setOnOpenEndHandler=function(func){  this.attachEvent("onOpenEnd",func);   };


    /**
*     @desc: enable/disable editor of tree cell ; enabled by default
*     @param: mode -  (boolean) true/false
*     @type: public
*     @topic: 0
*/
   dhtmlXGridObject.prototype.enableTreeCellEdit=function(mode){
        this._edtc=!convertStringToBoolean(mode);
    };


//#}

/**
*   @desc: return level of treeGrid row
*   @param: rowId - id of row
*   @type:  public
*   @returns: level of treeGrid row
*   @topic: 7
*/
dhtmlXGridObject.prototype.getLevel=function(rowId){      
        var z=this._h2.get[rowId||0];
        if (!z) return -1;
        return z.level;
}

dhtmlXGridObject.prototype._fixHiddenRowsAllTG=function(ind,state){
  for (i in this.rowsAr){
     if ((this.rowsAr[i])&&(this.rowsAr[i].childNodes))
        this.rowsAr[i].childNodes[ind].style.display=state;
  }
}

dhtmlXGridObject._emptyLineImg="blank";
//(c)dhtmlx ltd. www.dhtmlx.com