/**
 * JS App for Custom Canvas Tool
 *  - For drawing cable combinations which involves images and text generation,
 *  - Maintaining a sequence for the same, and switching between the sequences drawn
 *
 * Author: Gurpreet S Chahal
 *
 * Key Notes:
 *  1. WIRE_TYPE = array(strings) [simplex,duplex]
 *  2. DEFAULT_WIRE_COLOR: supported css values for a color
 *    i.e. colorname/HEX/RGB
 *  3. For Print
 *     - External library used.
 *     - http://printjs.crabbly.com/
 *
 * A Word on the architecture:
 * 1. The CanvasTool has a core CanvasTool class and a Global CanvasGUI class which acts as a wrapper.
 * 2.
 *
 * How To Run
 * var c = new CanvasTool(elemSelector); //Init CanvasTool
 *      elemSelector: String|prefix(#/.)|default(#canvas_config2)
 * c.initTool(); // Draw a Wire(rectangle), 10 pixels width
 * c.clearCanvas(); // Resets the canvas with a blank surface
 * c.drawBoots(bootImage);
 *      Draws boots at both ends of the wire.
 *      bootImage to draw the L and/or Right boots
 *
 * c.drawConnectors(connectorImageUrl);
 *      Draws connectors at both ends of the wire.
 *      connectorImageUrl to draw the L and/or Right boots
 *
 */

( function(){


if ($('#mega-menu-3472-0-0').is(':empty')){
    $("#mega-menu-3472-0-0").remove();
}
if ($('#mega-menu-3472-0-1').is(':empty')){
    $("#mega-menu-3472-0-1").remove();
}
if ($('#mega-menu-3472-0-2').is(':empty')){
    $("#mega-menu-3472-0-2").remove();
}
if ($('#mega-menu-3472-0-3').is(':empty')){
    $("#mega-menu-3472-0-3").remove();
}


if ($('#mega-menu-3472-1-0').is(':empty')){
    $("#mega-menu-3472-1-0").remove();
}
if ($('#mega-menu-3472-1-1').is(':empty')){
    $("#mega-menu-3472-1-1").remove();
}
if ($('#mega-menu-3472-1-2').is(':empty')){
    $("#mega-menu-3472-1-2").remove();
}
if ($('#mega-menu-3472-1-3').is(':empty')){
    $("#mega-menu-3472-1-3").remove();
}


//var row0_size = ($('#mega-menu-3472-0 ul .mega-sub-menu').size())/2;
//var row1_size = ($('#mega-menu-3472-1 ul .mega-sub-menu').size())/2;
var row0_size = ($('#mega-menu-3472-0 ul .mega-sub-menu').length)/2;
var row1_size = ($('#mega-menu-3472-1 ul .mega-sub-menu').length)/2;
//console.log("row0_size = " + row0_size);
//console.log("row1_size = " + row1_size);


var row0_required = 4 - row0_size;
//console.log("row0_required = " + row0_required);
if(row0_required > 0)
{
    if(row1_size > row0_required)
    {    
        for (i = 0; i < row0_required; ++i)
        {                                          
           $('#mega-menu-3472-0').append($('#mega-menu-3472-1-'+i));
        }
    }

    if(row1_size <= row0_required)
    {    
        for (i = 0; i < row1_size; ++i)
        {                                          
           $('#mega-menu-3472-0').append($('#mega-menu-3472-1-'+i));
        }
    }
}


    /**
     * Represents a CanvasTool Instance
     */
    this.CanvasTool = function() {

        //Create Global Element references
        this.VERSION='0.9.2';
 
        this.MEDIA_DIR = BASE_PATH+"/wp-content/uploads/customcable/canvas/";

        this.defaultWireTypes = [];
        this.wireType = {type: 'simplex', wiresCount: 0};//simplex|duplex|fanout
        this.DEFAULT_FONT_SIZE = '13px';
        this.DEFAULT_FONT_FAMILY = 'Signika';
        this.canvas_center = [];
        this.options = {};
        this.canvasDim = {};
        this.canvasWireDim = {};
        this.USER_DISCOUNT_PERCENTAGE = USER_DISCOUNT_PERCENTAGE;

        this.hardcodedConditions = {
            cassette: {
                connectorImg: this.MEDIA_DIR+'MTP_MPO_cassettes_connectors.png',
            }
        }

        this.drawnConnectorsDim = {
            left: {
                x: 0,
                y: 0,
                w: 0,
                h: 0
            },
            right: {
                x: 0,
                y: 0,
                w: 0,
                h: 0
            }
        };
        this.drawnBootsDim = {
            left: {
                x: 0,
                y: 0,
                w: 0,
                h: 0
            },
            right: {
                x: 0,
                y: 0,
                w: 0,
                h: 0
            }
        };

        this.wireStroke = 1.5;

        this.canvasWireDim.height = 10;
        
        this.bootImageHeight = 28;
        this.connectorImageHeight = 60;
        
        this.setBootImageHeight = function(height) {
            this.bootImageHeight = height;
            return;
        }

        this.setConnectorImageHeight = function(height) {
            this.connectorImageHeight = height;
            return;
        }

        this.activeGroup = '';

        this.setActiveGroup = function() {
            this.activeGroup = $('ol#steps-customs').find('li.parent-element').eq(0).data('group-name').toLowerCase();

            if (this.activeGroup.indexOf('copper') !== -1) {
                this.setBootImageHeight(45);
                this.setConnectorImageHeight(50);
            }
        }

        this.setActiveGroup();

        this.exceptions = {
            pigtail: {
                left: false,
                right: false
            },
            e2000: {
                left: false,
                right: false
            },
            uniboot: {
                left: false,
                right: false
            },
            mtp: {
                left: false,
                right: false
            },
            cassettes: {
                status: false,
            },
            nodeHousing: {
                left: false,
                right: false
            },

            /**
             * Custom Static exceptions for uniboots
             *  If the group name belongs one of
             *   - Indoor Jumpers
             *   - Test Reference Coords
             *  Only then display the Pigtails connector
             */

            //blue, orange, green, brown, grey, white
            fanoutMultiColors: ['#1588c9', 'orange', '#4dab48', '#b52525', 'grey', 'white'],

        }

        this.updateExceptions = function(key, val, subkey) {
        	if (subkey === undefined)
        		subkey = false;

            if (! this.exceptions.hasOwnProperty(key))
                return;

            if (subkey != false)
                this.exceptions[key][subkey] = val;
            else
                this.exceptions[key] = val;
            return;
        }

        this.activeStaticConds = {
            connectors: [],
            boot_type: [],
            cable: []
        }
        this.defaultWireColor = 'yellow';
        this.defaultStrokeColor = 'black';

        var default_boot_l_image = this.MEDIA_DIR+'boot_ribbed_green.png';
        var default_boot_r_image = this.MEDIA_DIR+'boot_ribbed_green.png';

        var default_connector_l_image = this.MEDIA_DIR+'connector_odvalc_black.png';
        var default_connector_r_image = this.MEDIA_DIR+'connector_odvalc_black.png';

        this.drawnFiguresLog = [];
        this.price = 0;
        this.user_discount = 0;
        this.partNumber = 'XXXXXXXXXX';
        this.isLastStepDone = false;

        this.setWireColor = function(color) {
            if (color !== undefined && color != "" && color !== null)
                this.defaultWireColor = color;
            else
                this.defaultWireColor = 'yellow';

            return;
        }

        this.resetPrice = function(CGuiObj) {
            this.price = '';
            this.user_discount = 0.00;
            updateGui('price', CGuiObj);
            updateGui('resetSpecialOrderBtn', CGuiObj, true);
            // try param to reset the val
            updateGui('length', CGuiObj, true);
            /*updateGui('userDiscount', CGuiObj, true);*/
        }

        this.updateUserDiscount = function(amt) {
            this.user_discount = amt.toFixed(2);
            return this.user_discount;
        }

        this.hidePrintBtn = function(CGuiObj) {
            updateGui('print', CGuiObj, true);
        }

        this.resetPrintBtn = function(CGuiObj) {
            this.isLastStepDone = false;
            if ( jQuery('ol#steps-customs').find('li.parent-element').last().hasClass("clicked-item") )
                this.isLastStepDone = true;
            updateGui('print', CGuiObj);
        }

        /**
         * To track whether or not to enable or disable price generation feature
         * The
         */
        this.isPriceActive = false;

        this.setPriceStatus = function(status) {
            this.isPriceActive = false;
            if (status)
                this.isPriceActive = true;
        }

        this.resetWeight = function(CGuiObj) {
            this.weight = '';
            updateGui('weight', CGuiObj);
        }

        this.resetLength = function(CGuiObj) {
            // tru param to reset the val
            updateGui('length', CGuiObj, true);
        }

        this.updatePrice = function(CGuiObj) {
            this.price = updatePrice(CGuiObj);

            // Update the GUI for price
            updateGui('price', CGuiObj);
            updateGui('resetSpecialOrderBtn', CGuiObj);
            /*updateGui('userDiscount', CGuiObj);*/
        }

        this.updateWeight = function(CGuiObj) {
            this.weight = updateWeight(CGuiObj);
            // Updatethe GUI for weight
            updateGui('weight', CGuiObj, true)
        }

        this.resetPartNumber = function(CGuiObj) {
            this.partNumber = 'XXXXXXXXXX';
            // Hiding the part-number  along with resetting
            updateGui('part-number', CGuiObj, true);
        }

        this.updatePartNumber = function(CGuiObj) {
            this.partNumber = generatePartNumber(CGuiObj);
            updateGui('part-number', CGuiObj);
        }

        this.resetBreakoutOptions = function() {
            $('ol#steps-customs')
                .find('li[data-config-name="breakout_options"]')
                .find('ul.sub-content select')
                .prop('selectedIndex', 0);
        }

        this.showResetBtn = function() {
            updateGui('resetBtn');
        }

        this.loadingProcessesQueue = [];

        this.showLoader = function(status, callerFunc) {
        	if (status === undefined)
        		status = true;
        	if (callerFunc === undefined)
        		callerFunc = '';

            if (status == true) {
                showLoader(status);
                this.loadingProcessesQueue.push(callerFunc);
                return;
            }

            var removeElemKey = -1;
            for (var i=0; i<this.loadingProcessesQueue.length; i++) {
                if (callerFunc == this.loadingProcessesQueue[i]) {
                    removeElemKey = i;
                    break;
                }
            }
            if (removeElemKey > -1)
                this.loadingProcessesQueue.splice(removeElemKey, 1);

            // Check if loading processQueue is empty
            if (this.loadingProcessesQueue.length == 0)
                showLoader(false);

        }

        this.clearSideWires = function(type) {
        	if (type === undefined)
        		type='right';

            if (type == 'right') {

                // Clear-out incase if fanouts
                if (this.wireType.type == 'duplex') {
                    var x = this.canvasWireDim.coordX1;
                    var y = this.canvasWireDim.coordY1;
                    var w = this.canvasWireDim.coordW;

                    var wireDiff = 11;
                    var wireStroke = this.wireStroke;
                    var color_for_wire = this.defaultWireColor;
                    var color_for_stroke = this.defaultStrokeColor;

                    var h = this.canvasWireDim.height;

                    this.context.clearRect(x+w, y-130, w+w/2+100, 240);

                    this.context.fillStyle = color_for_stroke;
                    this.context.fillRect(x+w-1, y, w+w-67+2, h);
                    this.context.fillStyle = color_for_wire;
                    // console.log("Redrawing the wire for duplex:", x, y, wireStroke, w, h);
                    this.context.fillRect(x+w-2, y+wireStroke, w+w-67+1, h-(2*wireStroke));

                    y = y + wireDiff;
                    this.context.fillStyle = color_for_stroke;
                    this.context.fillRect(x+w-1, y, w+w-67+2, h);
                    this.context.fillStyle = color_for_wire;
                    // console.log("Redrawing the wire for duplex:", x, y, wireStroke, w, h);
                    this.context.fillRect(x+w-2, y+wireStroke, w+w-67+1, h-(2*wireStroke));
                }

                // Clear-out incase if fanouts
                if (this.wireType.type == 'fanout') {
                    var x = this.canvasWireDim.coordX1;
                    var y = this.canvasWireDim.coordY1;
                    var w = this.canvasWireDim.coordW;
                    // clearSideWires(type, x, y, w, this);
                    var dx = x + w + 58;
                    if (this.exceptions.nodeHousing.right)
                        dx = x + w + 84;
                    this.context.clearRect(dx, y-130, w+w/2, 295);
                    // console.log("Clearing canvas at x:", x, 'y:', y, 'w:', w, 'h:', h);

                }

            }
        }

        this.logoImgUrl = '';
        this.updateLogoImgUrl = function(imgUrl) {
            this.logoImgUrl = imgUrl;
        }

        this.addLogoToCanvas = function(canvas, context) {
        	if (canvas === undefined)
        		canvas=false;
        	if (context === undefined)
        		context=false;

            var imgUrl = this.logoImgUrl;
            // var canvas = document.getElementById('canvas_config2');
            // var context = canvas.getContext('2d');
            if (!canvas) {
                var canvas = this.canvas;
                var context = this.context;
            }
            var imageObj = new Image();
            var width = 152;
            var height = 97;
            var cord_x = canvas.width - width - 10;
            imageObj.onload = function () {
                context.clearRect(0, 0, canvas.width, canvas.height);
                context.drawImage(imageObj, cord_x - 25, 15, width, height);
            };
            imageObj.src = imgUrl;
        }

        function generatePartNumber(CGuiObj) {

            var partNumberStr = '';
            for (var i=0; i<CGuiObj.selectedOptions.length; i++) {
                if (CGuiObj.selectedOptions[i].key == 'length') {
                    partNumberStr += '-'+CGuiObj.selectedOptions[i].value.userInput+CGuiObj.selectedOptions[i].value.unitPartNumberSelected;
                }
                else if(CGuiObj.selectedOptions[i].key == 'breakout_options') {
                    if (CGuiObj.selectedOptions[i].value.partNumberSideA != '') {
                        partNumberStr += CGuiObj.selectedOptions[i].value.partNumberSideA;
                    }
                    if (CGuiObj.selectedOptions[i].value.partNumberSideB != '') {
                        partNumberStr += CGuiObj.selectedOptions[i].value.partNumberSideB;
                    }
                    if (CGuiObj.selectedOptions[i].value.partNumberFurcation != '' && CGuiObj.selectedOptions[i].value.partNumberFurcation != 'undefined') {
                        partNumberStr += CGuiObj.selectedOptions[i].value.partNumberFurcation;
                    }
                }
                else{
                    if (CGuiObj.selectedOptions[i].value.cguiComponentPartNumber == undefined)
                        continue;
                    partNumberStr += CGuiObj.selectedOptions[i].value.cguiComponentPartNumber.toUpperCase();
                }
            }

            return partNumberStr;
        }


        this.drawnLog = {

            lastDrawn: {},

            /**
             * Sample properties for stack
             *  @type: objType,
             *  @dim:  objToLog,
             */
            stack: [],
        };

        this.textConfig = {
            left: [],
            right: [],
            top: [],
            bottom: [],
            bottom_2: [],
            lineHeight: 24
        }

        this.textLabels = {
            left:   [],
            top:    [],
            right:  [],
            bottom: [],
        }

        this.selectedConds = {
            db: {},
            staticConds: {}
        }

        this.activeConds = {
            db: {},
            staticConds: {}
        }

        this.updateActiveConds = function(key, obj) {
            if (key == 'db')
                this.activeConds.db = obj;

            if (key == 'staticConds')
                this.activeConds.staticConds = obj;

            return;
        }

        this.updateSelectedConds = function(type, obj) {
            if (type == undefined)
                return;
            if (type == 'db')
                this.selectedConds.db = obj;
            if (type == 'static')
                this.selectedConds.staticConds = obj;

            return;
        }

        this.history = [];
        this.currentHistoryIndex = 0;

        //Define Option Defaults
        var defaults = {
            /**
             * Sample wire types
             *  simplex|duplex|fanouts
             */
            wireType: 'simplex',
            canvasSelector: '#canvas_config2',
        }

        this.options.canvasSelector = defaults.canvasSelector;

        //Create options by defaults with the passed in arguments
        if (arguments[0] && typeof arguments[0] === 'object') {
            this.options = extendDefaults(defaults, arguments[0]);
        } else {
            this.options = extendDefaults(defaults, {});
        }

        // Setting up the canvas
        this.initCanvas = function() {

            this.canvas = setCanvas(this.options.canvasSelector);
            this.context = setContext(this.canvas);

            this.canvasDim.width = this.canvas.width;
            this.canvasDim.height = this.canvas.height;
            this.canvasDim.centreX = this.canvas.width/2;
            this.canvasDim.centreY = this.canvas.height/2;
            this.canvasDim.margin = this.canvas.width/4;
            this.canvasDim.pipeRadius = 10;

            var bg_box_width = 27;
            this.canvasDim.boxMargin = bg_box_width*2;

            this.textConfig = {
                top_left: {
                    x:0+(this.canvasDim.boxMargin),
                    y:0+(this.canvasDim.boxMargin),
                    w:(this.canvasDim.width/3),
                },
                left: {
                    x:0+(2*this.canvasDim.boxMargin),
                    y:this.canvasDim.centreY+this.canvasDim.height/2-(this.canvasDim.height)/4,
                    w:(this.canvasDim.width/5),
                },
                left_2: {
                    x:0+(2*this.canvasDim.boxMargin),
                    y:this.canvasDim.centreY+this.canvasDim.height/2-(this.canvasDim.height)/4+20,
                    w:(this.canvasDim.width/5),
                },
                left_3: {
                    x:0+(2*this.canvasDim.boxMargin),
                    y:this.canvasDim.centreY+this.canvasDim.height/2-(this.canvasDim.height)/4+40,
                    w:(this.canvasDim.width/5),
                },
                top: {
                    x:this.canvasDim.centreX-(this.canvasDim.margin/2),
                    y:this.canvasDim.centreY-(this.canvasDim.margin),
                    w:(this.canvasDim.width/5),
                },
                right: {
                    x:this.canvasDim.width-(this.canvasDim.margin/2),
                    y:this.canvasDim.centreY+this.canvasDim.height/2-(this.canvasDim.height)/4,
                    w:(this.canvasDim.width/5),
                },
                right_2: {
                    x:this.canvasDim.width-(this.canvasDim.margin/2),
                    y:this.canvasDim.centreY+this.canvasDim.height/2-(this.canvasDim.height)/4+20,
                    w:(this.canvasDim.width/5),
                },
                right_3: {
                    x:this.canvasDim.width-(this.canvasDim.margin/2),
                    y:this.canvasDim.centreY+this.canvasDim.height/2-(this.canvasDim.height)/4+40,
                    w:(this.canvasDim.width/5),
                },
                bottom: {
                    x:this.canvasDim.centreX,
                    y:this.canvasDim.centreY+this.canvasDim.height/2-(this.canvasDim.height)/4,
                    w:(this.canvasDim.width/3),
                },

            }

            if (this.wireType.type == 'simplex') {
                this.canvasWireDim.width = this.canvasDim.width/2 + (4*bg_box_width);
                this.canvasWireDim.coordX1 = this.canvasDim.centreX - this.canvasDim.margin - (2*bg_box_width);
                this.canvasWireDim.coordY1 = this.canvasDim.centreY-this.canvasWireDim.height;
                this.canvasWireDim.coordX2 = this.canvasDim.width - this.canvasDim.margin;
                this.canvasWireDim.coordY2 = this.canvasDim.centreY+this.canvasWireDim.height/2;

            }
            if (this.wireType.type == 'duplex') {

                var rtWireJoinHack = 2;
                this.canvasWireDim.width = this.canvasDim.width/2 + (4*bg_box_width);
                this.canvasWireDim.coordW  = (this.canvasDim.width/2 + (4*bg_box_width))/4;
                this.canvasWireDim.coordH  = this.canvasDim.height/2;
                this.canvasWireDim.coordX1 = this.canvasDim.centreX - this.canvasWireDim.coordW/2;
                this.canvasWireDim.coordY1 = this.canvasDim.centreY-this.canvasWireDim.height;

                var widthForSubWires = (this.canvasDim.width/2 + (4*bg_box_width))*(3/8);
                this.canvasWireDim.coordLtW  = widthForSubWires - (4*this.canvasDim.pipeRadius);
                this.canvasWireDim.coordLtH  = this.canvasWireDim.height/2;
                this.canvasWireDim.coordLtX1 = 0 + this.canvasDim.margin - (2*bg_box_width);//this.canvasDim.centreX - this.canvasDim.margin - (2*bg_box_width);
                this.canvasWireDim.coordLtY1 = this.canvasDim.centreY-this.canvasWireDim.height- (4*this.canvasDim.pipeRadius);

                this.canvasWireDim.coordLbX1 = this.canvasDim.centreX - this.canvasDim.margin - (2*bg_box_width);//this.canvasDim.centreX - this.canvasDim.margin - (2*bg_box_width);
                this.canvasWireDim.coordLbY1 = this.canvasDim.centreY-this.canvasWireDim.height + 40;
                this.canvasWireDim.coordLbW  = widthForSubWires - (4*this.canvasDim.pipeRadius);
                this.canvasWireDim.coordLbH  = this.canvasWireDim.height/2;

                this.canvasWireDim.coordRtW  = widthForSubWires - (4*this.canvasDim.pipeRadius);
                this.canvasWireDim.coordRtH  = this.canvasWireDim.height/2;
                this.canvasWireDim.coordRtX1 = this.canvasDim.width - (2*this.canvasDim.margin) + (4*bg_box_width)+rtWireJoinHack;
                this.canvasWireDim.coordRtY1 = this.canvasDim.centreY-this.canvasWireDim.height- (4*this.canvasDim.pipeRadius);

                this.canvasWireDim.coordRbW  = widthForSubWires - (4*this.canvasDim.pipeRadius);
                this.canvasWireDim.coordRbH  = this.canvasWireDim.height/2;
                this.canvasWireDim.coordRbX1 = this.canvasDim.width - (2*this.canvasDim.margin) + (4*bg_box_width)+rtWireJoinHack;
                this.canvasWireDim.coordRbY1 = this.canvasDim.centreY-this.canvasWireDim.height + 40;

                this.drawnText = {
                    left:   {},
                    top:    {},
                    right:  {},
                    bottom: {},
                }
            }

            if (this.wireType.type == 'fanout') {

                var rtWireJoinHack = 2;

                this.canvasWireDim.coordW  = (this.canvasDim.width/2 + (4*bg_box_width))/4;
                this.canvasWireDim.coordH  = this.canvasDim.height/2;
                this.canvasWireDim.coordX1 = this.canvasDim.centreX - this.canvasWireDim.coordW/2;
                this.canvasWireDim.coordY1 = this.canvasDim.centreY-this.canvasWireDim.height;

                var widthForSubWires = (this.canvasDim.width/2 + (4*bg_box_width))*(3/8);
                this.canvasWireDim.coordLtW  = widthForSubWires - (4*this.canvasDim.pipeRadius);
                this.canvasWireDim.coordLtH  = this.canvasWireDim.height/2;
                this.canvasWireDim.coordLtX1 = 0 + this.canvasDim.margin - (2*bg_box_width);
                this.canvasWireDim.coordLtY1 = this.canvasDim.centreY-this.canvasWireDim.height- (4*this.canvasDim.pipeRadius);

                this.canvasWireDim.coordLbX1 = this.canvasDim.centreX - this.canvasDim.margin - (2*bg_box_width);
                this.canvasWireDim.coordLbY1 = this.canvasDim.centreY-this.canvasWireDim.height + 40;
                this.canvasWireDim.coordLbW  = widthForSubWires - (4*this.canvasDim.pipeRadius);
                this.canvasWireDim.coordLbH  = this.canvasWireDim.height/2;

                this.canvasWireDim.coordRtW  = widthForSubWires - (4*this.canvasDim.pipeRadius);
                this.canvasWireDim.coordRtH  = this.canvasWireDim.height/2;
                this.canvasWireDim.coordRtX1 = this.canvasDim.width - (2*this.canvasDim.margin) + (4*bg_box_width)+rtWireJoinHack;
                this.canvasWireDim.coordRtY1 = this.canvasDim.centreY-this.canvasWireDim.height- (4*this.canvasDim.pipeRadius);

                this.canvasWireDim.coordRbW  = widthForSubWires - (4*this.canvasDim.pipeRadius);
                this.canvasWireDim.coordRbH  = this.canvasWireDim.height/2;
                this.canvasWireDim.coordRbX1 = this.canvasDim.width - (2*this.canvasDim.margin) + (4*bg_box_width)+rtWireJoinHack;
                this.canvasWireDim.coordRbY1 = this.canvasDim.centreY-this.canvasWireDim.height + 40;

                this.drawnText = {
                    left:   {},
                    top:    {},
                    right:  {},
                    bottom: {},
                }

            }


        }

        this.initialConnectorsAndBoots = {
            connector_a: '',
            connector_b: '',
            boots: '',
        };

        this.logInitialConnectorsAndBoots = function() {
            this.initialConnectorsAndBoots.connector_a = $('ol#steps-customs').find('li.parent[data-config-name="connector_a"]').find('ul.sub-content').html();

            this.initialConnectorsAndBoots.connector_b = $('ol#steps-customs').find('li.parent[data-config-name="connector_b"]').find('ul.sub-content').html();

            this.initialConnectorsAndBoots.boot_type = $('ol#steps-customs').find('li.parent[data-config-name="boot_type"]').find('ul.sub-content').html();
        }

        this.logInitialConnectorsAndBoots();

        this.setWireType = function(type, wiresCount) {
        	if (type === undefined)
        		type = 'simplex';
        	if (wiresCount === undefined)
        		wiresCount = 0;

            return setWireType(this, type, wiresCount);
        }

        this.setWireStroke = function(stroke) {
        	if (stroke === undefined)
        		stroke=1.5;
            return this.wireStroke = stroke;
        }

        this.captureCanvasOutput = function(key) {
        	if (key === undefined)
        		key='';
            captureCanvasOutput(this, key);
        }

        this.drawWire = function(type, wiresCount) {
        	if (type === undefined)
        		type='simplex';
        	if (wiresCount === undefined)
        		wiresCount=0;
            this.setWireType(type, wiresCount);
            drawWire(this);
        }

        /**
         * Draw Boots
         *
         */
        this.drawBoots = function(type, img, captureCanvasOutputObj, rightDrawFlag, subMenuClickedDatasetObj) {
            if (type === undefined)
            	type = 'left';
            if (captureCanvasOutputObj.status === undefined)
            	captureCanvasOutputObj.status = false;
           	if (captureCanvasOutputObj.componentKey === undefined)
            	captureCanvasOutputObj.componentKey = '';
            if (rightDrawFlag === undefined)
            	rightDrawFlag = true;
            
            // Debugging
            var imgL = default_boot_l_image;
            var imgR = default_boot_r_image;
            if(img !== undefined){
                imgL = this.MEDIA_DIR+img;
                imgR = this.MEDIA_DIR+img;
            }

            //update boot type for conn APC            
            var activeGroup = CGui.CanvasToolObj.activeGroup;            
            if(activeGroup != "copper products")
            {
            	var clicked_img = JSON.parse(subMenuClickedDatasetObj.canvasImage);
            	var clicked_img = JSON.parse(subMenuClickedDatasetObj.canvasImage);
	            if(activeGroup == "catv node cables")
	            {
					if(CGui.selectedOptions[4].value.cguiComponentName.indexOf('APC') !== -1)
					{
						for(var key in clicked_img)
						{
							var img_name = clicked_img[key]['img'];
							var img_status = clicked_img[key]['status'];
						}
						if(img_name.indexOf("green") !== -1 && img_status == 1)
						{
							var split_img = img.split(".");
							var split_name = split_img[0].split("_");
							var imgL = this.MEDIA_DIR+split_name[0]+"_"+split_name[1]+"_green."+split_img[1];
						}
						else
						{
							var imgL = this.MEDIA_DIR+"boot_ribbed_green.png";
						}
					}
					
					if(CGui.selectedOptions[5].value.cguiComponentName.indexOf('APC') !== -1)
					{
						for(var key in clicked_img)
						{
							var img_name = clicked_img[key]['img'];
							var img_status = clicked_img[key]['status'];
						}
						if(img_name.indexOf("green") !== -1 && img_status == 1)
						{
							var split_img = img.split(".");
							var split_name = split_img[0].split("_");
							var imgR = this.MEDIA_DIR+split_name[0]+"_"+split_name[1]+"_green."+split_img[1];
						}
						else
						{
							var imgR = this.MEDIA_DIR+"boot_ribbed_green.png";
						}					
					}
				}
				else
				{				
					if(CGui.selectedOptions[3].value.cguiComponentName.indexOf('APC') !== -1)
					{
						for(var key in clicked_img)
						{
							var img_name = clicked_img[key]['img'];
							var img_status = clicked_img[key]['status'];
						}
						if(img_name.indexOf("green") !== -1 && img_status == 1)
						{
							var split_img = img.split(".");
							var split_name = split_img[0].split("_");
							var imgL = this.MEDIA_DIR+split_name[0]+"_"+split_name[1]+"_green."+split_img[1];
						}
						else
						{
							var imgL = this.MEDIA_DIR+"boot_ribbed_green.png";
						}
					}
					
					if(CGui.selectedOptions[4].value.cguiComponentName.indexOf('APC') !== -1)
					{
						for(var key in clicked_img)
						{
							var img_name = clicked_img[key]['img'];
							var img_status = clicked_img[key]['status'];
						}
						if(img_name.indexOf("green") !== -1 && img_status == 1)
						{
							var split_img = img.split(".");
							var split_name = split_img[0].split("_");
							var imgR = this.MEDIA_DIR+split_name[0]+"_"+split_name[1]+"_green."+split_img[1];					
						}
						else
						{
							var imgR = this.MEDIA_DIR+"boot_ribbed_green.png";
						}	
					}
				}
			}
            if (this.wireType.type == 'simplex') {
                var w = this.canvasWireDim.width;
                var x = this.canvasWireDim.coordX1;
                var y  = this.canvasWireDim.coordY1;

                var dy = y - ((this.bootImageHeight - this.canvasWireDim.height)/2);

                if (type == 'left') {
                    canvasDrawImage(this, x, dy, imgL, 'leftBoot', this.drawnLog, captureCanvasOutputObj);
                }

                if (type == 'right' && rightDrawFlag == true) {
                    x=x+w;// this.drawnLog.stack[0].dim.w;
                    canvasDrawImage(this, x, dy, imgR, 'rightBoot',this.drawnLog, captureCanvasOutputObj);
                }

            }

            if (this.wireType.type == 'duplex') {

                var bg_box_width = 27;
                var wireDiff = 11;
                var color_for_wire = this.defaultWireColor;
                var color_for_stroke = this.defaultStrokeColor;
                var wireStroke = this.wireStroke;

                /**
                 * Left Boots
                 */

                if (! this.exceptions.uniboot.left) {

                    var w = this.canvasWireDim.coordLtW;
                    var x = this.canvasWireDim.coordLtX1;
                    var y = this.canvasWireDim.coordLtY1;

                    var dy = y - ((this.bootImageHeight - this.canvasWireDim.height)/2);

                    canvasDrawImage(this, x, dy, imgL, 'leftBoot', this.drawnLog, captureCanvasOutputObj);

                    var w = this.canvasWireDim.coordLbW;
                    var x = this.canvasWireDim.coordLbX1;
                    var y = this.canvasWireDim.coordLbY1;
                    y = y+11;

                    dy = y - ((this.bootImageHeight - this.canvasWireDim.height)/2);

                    canvasDrawImage(this, x, dy, imgL, 'leftBoot', this.drawnLog, captureCanvasOutputObj);

                }
                else
                {
                    var x = this.canvasDim.centreX - this.canvasDim.margin - (2*bg_box_width);
                    var y = this.canvasDim.centreY-this.canvasWireDim.height+(bg_box_width-5);
                    var w = this.canvasDim.width/2 + (4*bg_box_width);

                    var dy = y - ((this.connectorImageHeight - this.canvasWireDim.height)/2);

                    canvasDrawImage(this, x, dy, imgL, 'leftBoot', this.drawnLog, captureCanvasOutputObj);
                }


                /**
                 * Right Boots
                 */
                if (! this.exceptions.uniboot.right) {
                    var w = this.canvasWireDim.coordRtW;
                    var x = this.canvasWireDim.coordRtX1;
                    var y = this.canvasWireDim.coordRtY1;
                    x= x + w;

                    dy = y - ((this.bootImageHeight - this.canvasWireDim.height)/2);
                    //Incase of no type argument passed, drawing both images
                    if (rightDrawFlag == true)
                        canvasDrawImage(this, x, dy, imgR, 'rightBoot', this.drawnLog, captureCanvasOutputObj);


                    var w = this.canvasWireDim.coordRbW;
                    var x = this.canvasWireDim.coordRbX1;
                    var y = this.canvasWireDim.coordRbY1;
                    y = y+11;
                    x = x+w;

                    dy = y - ((this.bootImageHeight - this.canvasWireDim.height)/2);
                    if (rightDrawFlag == true)
                        canvasDrawImage(this, x, dy, imgR, 'rightBoot',this.drawnLog, captureCanvasOutputObj);
                }
                else
                {
                    var x = this.canvasDim.centreX - this.canvasDim.margin - (2*bg_box_width);
                    var y = this.canvasDim.centreY-this.canvasWireDim.height+(bg_box_width-4);
                    var w = this.canvasDim.width/2 + (4*bg_box_width);

                    x = x + w;

                    var dy = y - ((this.connectorImageHeight - this.canvasWireDim.height)/2);

                    canvasDrawImage(this, x, dy, imgR, 'rightBoot',this.drawnLog, captureCanvasOutputObj);
                }
            }

            if (this.wireType.type == 'fanout') {

                var drawnMtp = 0;
                var wiresCount = this.wireType.wiresCount;

                var staticWireHeight = this.canvasWireDim.height;
                var wireDiff = 11;
                var diffHeight = 20;

                var drawBootsNormallyForLeft = true;
                var drawBootsNormallyForRight = true;

                if ( (this.exceptions.mtp.left == true)  ) {

                    x = this.canvasWireDim.coordLtX1 - 27;
                    y = this.canvasWireDim.coordLtY1;
                    w = this.canvasWireDim.coordLtW;
                    h = staticWireHeight;

                    var dy = y - ((this.bootImageHeight - this.canvasWireDim.height)/2);
                    var hackForInnerHeight = 10;

                    if (this.wireType.wiresCount < 24) {
                        // Drawing #2 inner boots, For #4 and #2 case of mtp-fanouts
                        canvasDrawImage(this, x, dy+hackForInnerHeight + 41, imgL, 'leftBoot', this.drawnLog, captureCanvasOutputObj);
                    }
                    else
                    {
                        // Drawing #2 inner boots, For #4 and #2 case of mtp-fanouts
                        canvasDrawImage(this, x, dy+hackForInnerHeight + 3, imgL, 'leftBoot', this.drawnLog, captureCanvasOutputObj);
                        canvasDrawImage(this, x, dy+hackForInnerHeight + 75, imgL, 'leftBoot', this.drawnLog, captureCanvasOutputObj);
                    }

                    // * Draw only in case of wires-count for fanout above 24
                    if (this.wireType.wiresCount > 24) {
                        var topMagin = 85;
                        var bottomMargin = 85;
                        // Drawing #2 outer boots, Only For #4 case Mtp-fanouts

                        canvasDrawImage(this, x, dy+hackForInnerHeight - topMagin + 3, imgL, 'leftBoot', this.drawnLog, captureCanvasOutputObj);
                        canvasDrawImage(this, x, dy+hackForInnerHeight + 75 + bottomMargin, imgL, 'leftBoot', this.drawnLog, captureCanvasOutputObj);
                    }

                    drawBootsNormallyForLeft = false;

                    drawnMtp++;

                }
                if ( (this.exceptions.mtp.right == true) ) {

                    x = canvasWireDim.coordRtX1 + 27;
                    y = canvasWireDim.coordRtY1;
                    w = canvasWireDim.coordRtW;
                    h = staticWireHeight;
                    x = x+w;

                    var dy = y - ((this.bootImageHeight - this.canvasWireDim.height)/2);
                    var hackForInnerHeight = 10;

                    if (this.wireType.wiresCount < 24) {
                        // Drawing #2 inner connectors, For #4 and #2 case of mtp-fanouts
                        canvasDrawImage(this, x, dy+hackForInnerHeight + 41, imgR, 'rightBoot', this.drawnLog, captureCanvasOutputObj);
                    }
                    else
                    {
                        // Drawing #2 inner connectors, For #4 and #2 case of mtp-fanouts
                        canvasDrawImage(this, x, dy+hackForInnerHeight+3, imgR, 'rightBoot', this.drawnLog, captureCanvasOutputObj);
                        canvasDrawImage(this, x, dy+hackForInnerHeight + 75, imgR, 'rightBoot', this.drawnLog, captureCanvasOutputObj);
                    }

                    // * Draw only in case of wires-count for fanout above 24
                    if (this.wireType.wiresCount > 24) {
                        var topMagin = 85;
                        var bottomMargin = 85;
                        // Daring #2 outer connectors, Only For #4 case Mtp-fanouts

                        canvasDrawImage(this, x, dy+hackForInnerHeight - topMagin+3, imgR, 'rightBoot', this.drawnLog, captureCanvasOutputObj);
                        canvasDrawImage(this, x, dy+hackForInnerHeight + 75 + bottomMargin, imgR, 'rightBoot', this.drawnLog, captureCanvasOutputObj);
                    }

                    drawBootsNormallyForRight = false;

                    drawnMtp++;
                }

                if (drawBootsNormallyForLeft || drawBootsNormallyForRight){

                    var loopCounter = 0;
                    switch(parseInt(wiresCount)) {

                        case 4:
                            loopCounter = 2;
                            break;

                        case 6:
                            loopCounter = 3;
                            break;

                        default:
                            loopCounter = 3;
                            break;
                    }

                    var staticWireHeight = this.canvasWireDim.height;
                    var wireDiff = 11;
                    var diffHeight = 20;
                    for (var i=loopCounter; i>0; i--) {

                        /**
                         *  Wire #2
                         *  Left-Upper curve
                         */
                        x = canvasWireDim.coordLtX1 - 27;
                        y = canvasWireDim.coordLtY1;
                        w = canvasWireDim.coordLtW;
                        h = staticWireHeight;
                        if (i>1){
                            y = y - (i*diffHeight);
                        }
                        if (i == loopCounter)
                            y = y - diffHeight;

                        var dy = y - ((this.bootImageHeight - this.canvasWireDim.height)/2);

                        if (drawBootsNormallyForLeft == true)
                            canvasDrawImage(this, x, dy, imgL, 'leftBoot', this.drawnLog, captureCanvasOutputObj);


                        /**
                         *  Wire #2
                         *  Left-Bottom curve
                         */

                        x = canvasWireDim.coordLbX1 - 27;
                        y = canvasWireDim.coordLbY1;
                        w = canvasWireDim.coordLbW;
                        h = staticWireHeight;
                        if (i>1){
                            y = y+(i*diffHeight);
                        }
                        if (i == loopCounter)
                            y = y + diffHeight;
                        y = y + wireDiff + 5;

                        dy = y - ((this.bootImageHeight - this.canvasWireDim.height)/2);

                        if (drawBootsNormallyForLeft == true)
                            canvasDrawImage(this, x, dy, imgL, 'leftBoot', this.drawnLog, captureCanvasOutputObj);

                        /**
                         * Right Top
                         */
                        x = canvasWireDim.coordRtX1 + 27;
                        y = canvasWireDim.coordRtY1;
                        w = canvasWireDim.coordRtW;
                        h = staticWireHeight;
                        x = x+w;
                        if (i>1){
                            y = y - (i*diffHeight);
                        }
                        if (i == loopCounter)
                            y = y - diffHeight;

                        dy = y - ((this.bootImageHeight - this.canvasWireDim.height)/2);
                        //Incase of no type argument passed, drawing both images
                        if (drawBootsNormallyForRight == true && rightDrawFlag == true)
                            canvasDrawImage(this, x, dy, imgR, 'rightBoot', this.drawnLog, captureCanvasOutputObj);


                        /**
                         *  Wire #2
                         *  Right-Bottom curve
                         */
                        x = canvasWireDim.coordRbX1 + 27;
                        y = canvasWireDim.coordRbY1;
                        w = canvasWireDim.coordRbW;
                        h = staticWireHeight;
                        x = x + w;
                        if (i>1) {
                            y = y + (i*diffHeight);
                        }
                        if (i == loopCounter)
                            y = y + diffHeight;
                        y = y + wireDiff + 5;
                        dy = y - ((this.bootImageHeight - this.canvasWireDim.height)/2);

                        if (drawBootsNormallyForRight == true && rightDrawFlag == true)
                            canvasDrawImage(this, x, dy, imgR, 'rightBoot',this.drawnLog, captureCanvasOutputObj);

                    }
                }
            }
            return true;
        }

        this.drawCassetteBox = function(img) {

            if (img === undefined)
                return;

            var x = this.canvasDim.centreX;
            var y  = this.canvasDim.centreY;

            canvasDrawImage(this, x, y, this.MEDIA_DIR+img, 'cassette', this.drawnLog, {});
        }

        this.drawCassetteConnector = function(img) {

            if (img === undefined)
                return;

            var x = this.canvasDim.centreX;
            var y  = this.canvasDim.centreY;

            canvasDrawImage(this, x, y, this.MEDIA_DIR+img, 'cassetteConnector', this.drawnLog, {});

        }

        this.drawNodeHousing = function(type, image) {
        	if (type === undefined)
        		type='side-a';
            // Drawing a single wire (side-a|left) incase of single selected
            var x = this.canvasWireDim.coordX1 - 30;
            var y = this.canvasWireDim.coordY1;
            var w = this.canvasWireDim.coordW + 60;
            var h = this.canvasWireDim.height;

            this.updateExceptions('nodeHousing', true, 'left');
            if (type == 'side-a') {
                this.updateExceptions('nodeHousing', false, 'right');
                canvasDrawImage(this, x - 55, y+14, this.MEDIA_DIR+image, 'leftNodeHousing', this.drawnLog, {});
            }
            else
            {
                this.updateExceptions('nodeHousing', true, 'right');
                canvasDrawImage(this, x + 130, y+14, this.MEDIA_DIR+image, 'rightNodeHousing', this.drawnLog, {});
            }
        }

        /**
         *
         * Draw connectors
         */
        this.drawConnectors = function(type, img, exception, captureCanvasOutputObj) {
        	if (type === undefined)
        		type="left";
        	if (exception === undefined)
        		exception=false;
        	if (captureCanvasOutputObj.status === undefined)
        		captureCanvasOutputObj.status = false;
        	if (captureCanvasOutputObj.componentKey === undefined)
        		captureCanvasOutputObj.componentKey = '';

            var imgL = default_connector_l_image;
            var imgR = default_connector_r_image;
            if(img !== undefined){
                imgL = this.MEDIA_DIR+img;
                imgR = this.MEDIA_DIR+img;
            }
            if (this.wireType.type == 'simplex') {
                var w = this.canvasWireDim.width;
                var x = this.canvasWireDim.coordX1;
                var y  = this.canvasWireDim.coordY1;

                if (exception){
                    x = x + 32;
                    y = y - 5;
                }


                var dy = y - ((this.connectorImageHeight - this.canvasWireDim.height)/2);

                if (type == 'left') {
                    var drawnDim = canvasDrawImage(this, x, dy, imgL, 'leftConnector', this.drawnLog, captureCanvasOutputObj);
                }

                if (type == 'right') {
                    x = x + w;
                    if(exception){
                        x = x - 64;
                        y = y - 5;
                    }
                    var drawnDim = canvasDrawImage(this, x, dy, imgL, 'rightConnector', this.drawnLog, captureCanvasOutputObj);
                }

            }

            if (this.wireType.type == 'duplex') {

                redrawDuplexWire(this, type);

                var bg_box_width = 27;
                var wireDiff = 11;
                var color_for_wire = this.defaultWireColor;
                var color_for_stroke = this.defaultStrokeColor;
                var wireStroke = this.wireStroke;

                if (type == 'left') {
                    var w = this.canvasWireDim.coordLtW;
                    var x = this.canvasWireDim.coordLtX1;
                    var y = this.canvasWireDim.coordLtY1;

                    // If uniboot, or other exception, drawing single image, rather than 2
                    if (! this.exceptions.uniboot.left) {
                        if (exception){
                            x = x + 32;
                            y = y - 5;
                        }

                        var dy = y - ((this.connectorImageHeight - this.canvasWireDim.height)/2);

                        canvasDrawImage(this, x, dy, imgL, 'leftConnector', this.drawnLog, captureCanvasOutputObj);


                        var w = this.canvasWireDim.coordLbW;
                        var x = this.canvasWireDim.coordLbX1;
                        var y = this.canvasWireDim.coordLbY1;
                        y = y + 11;

                        if (exception){
                            x = x + 32;
                            y = y - 5;
                        }

                        dy = y - ((this.connectorImageHeight - this.canvasWireDim.height)/2);

                        canvasDrawImage(this, x, dy, imgL, 'leftConnector', this.drawnLog, captureCanvasOutputObj);
                    }
                    else {
                        var x = this.canvasDim.centreX - this.canvasDim.margin - (2*bg_box_width);
                        var y = this.canvasDim.centreY-this.canvasWireDim.height + bg_box_width/5;
                        var w = this.canvasDim.width/2 + (4*bg_box_width);

                        if (exception){
                            x = x + 32;
                            y = y - 5;
                        }

                        var dy = y - ((this.connectorImageHeight - this.canvasWireDim.height)/2);

                        canvasDrawImage(this, x, dy, imgL, 'leftConnector', this.drawnLog, captureCanvasOutputObj);
                    }
                }

                if (type == 'right') {
                    //Right connectors
                    var w = this.canvasWireDim.coordRtW;
                    var x = this.canvasWireDim.coordRtX1;
                    x = x+w;
                    var y = this.canvasWireDim.coordRtY1;

                    // If uniboot, or other exception, drawing single image, rather than 2
                    if (! this.exceptions.uniboot.right) {
                        if(exception){
                            x = x - 32;
                            y = y - 5;
                        }

                        dy = y - ((this.connectorImageHeight - this.canvasWireDim.height)/2);

                        canvasDrawImage(this, x, dy, imgR, 'rightConnector', this.drawnLog, captureCanvasOutputObj);

                        var w = this.canvasWireDim.coordRbW;
                        var x = this.canvasWireDim.coordRbX1;
                        var y = this.canvasWireDim.coordRbY1;
                        y = y + 11;
                        x = x+w;

                        if(exception){
                            x = x - 32;
                            y = y - 5;
                        }

                        dy = y - ((this.connectorImageHeight - this.canvasWireDim.height)/2);

                        canvasDrawImage(this, x, dy, imgR, 'rightConnector', this.drawnLog, captureCanvasOutputObj);
                    }
                    else
                    {
                        var x = this.canvasDim.centreX - this.canvasDim.margin - (2*bg_box_width);
                        var y = this.canvasDim.centreY-this.canvasWireDim.height+bg_box_width/4;
                        var w = this.canvasDim.width/2 + (4*bg_box_width);

                        x = x + w;

                        if (exception){
                            x = x - 32;
                            y = y - 5;
                        }

                        var dy = y - ((this.connectorImageHeight - this.canvasWireDim.height)/2);
                        canvasDrawImage(this, x, dy, imgR, 'rightConnector', this.drawnLog, captureCanvasOutputObj);
                    }
                }
            }

            if (this.wireType.type == 'fanout') {

                redrawFanoutWire(this, type);

                var wiresCount = this.wireType.wiresCount;

                var loopCounter = 0;
                switch(parseInt(wiresCount)) {

                    case 4:
                        loopCounter = 2;
                        break;

                    case 6:
                        loopCounter = 3;
                        break;

                    default:
                        loopCounter = 3;
                        break;
                }

                var staticWireHeight = this.canvasWireDim.height;
                var wireDiff = 11;
                var diffHeight = 20;

                if (this.exceptions.mtp.left && type == 'left') {

                    x = this.canvasWireDim.coordLtX1 - 27;
                    y = this.canvasWireDim.coordLtY1;
                    w = this.canvasWireDim.coordLtW;
                    h = staticWireHeight;

                    if (exception){
                        x = x + 32;
                        y = y - 5;
                    }

                    var dy = y - ((this.connectorImageHeight - this.canvasWireDim.height)/2);

                    var hackForInnerHeight = 10;

                    if (this.wireType.wiresCount < 24 )
                    {
                        // Drawing #2 inner connectors, For #4 and #2 case of mtp-fanouts
                        canvasDrawImage(this, x, dy+hackForInnerHeight + 39, imgL, 'leftConnector', this.drawnLog, captureCanvasOutputObj);
                    }
                    else {
                        // Drawing #2 inner connectors, For #4 and #2 case of mtp-fanouts
                        canvasDrawImage(this, x, dy+hackForInnerHeight, imgL, 'leftConnector', this.drawnLog, captureCanvasOutputObj);
                        canvasDrawImage(this, x, dy+hackForInnerHeight + 73, imgL, 'leftConnector', this.drawnLog, captureCanvasOutputObj);
                    }

                    // * Draw only in case of wires-count for fanout above 24
                    if (this.wireType.wiresCount > 24) {
                        var topMagin = 85;
                        var bottomMargin = 85;
                        // Daring #2 outer connectors, Only For #4 case Mtp-fanouts
                        canvasDrawImage(this, x, dy+hackForInnerHeight - topMagin, imgL, 'leftConnector', this.drawnLog, captureCanvasOutputObj);
                        canvasDrawImage(this, x, dy+hackForInnerHeight + 73 + bottomMargin, imgL, 'leftConnector', this.drawnLog, captureCanvasOutputObj);
                    }


                }
                else if (this.exceptions.mtp.right && type == 'right') {
                    x = canvasWireDim.coordRtX1 + 27;
                    y = canvasWireDim.coordRtY1;
                    w = canvasWireDim.coordRtW;
                    h = staticWireHeight;
                    x = x+w;

                    if(exception){
                        x = x - 32;
                        y = y - 5;
                    }

                    var dy = y - ((this.connectorImageHeight - this.canvasWireDim.height)/2);

                    var hackForInnerHeight = 10;


                    if (this.wireType.wiresCount < 24) {
                        canvasDrawImage(this, x, dy+hackForInnerHeight + 39, imgR, 'rightConnector', this.drawnLog, captureCanvasOutputObj);
                    }
                    else
                    {
                        // Drawing #2 inner connectors, For #4 and #2 case of mtp-fanouts
                        canvasDrawImage(this, x, dy+hackForInnerHeight, imgR, 'rightConnector', this.drawnLog, captureCanvasOutputObj);
                        canvasDrawImage(this, x, dy+hackForInnerHeight + 73, imgR, 'rightConnector', this.drawnLog, captureCanvasOutputObj);
                    }

                    // * Draw only in case of wires-count for fanout above 24
                    if (this.wireType.wiresCount > 24) {
                        var topMagin = 85;
                        var bottomMargin = 85;
                        // Daring #2 outer connectors, Only For #4 case Mtp-fanouts
                        canvasDrawImage(this, x, dy+hackForInnerHeight - topMagin, imgR, 'rightConnector', this.drawnLog, captureCanvasOutputObj);
                        canvasDrawImage(this, x, dy+hackForInnerHeight + 73 + bottomMargin, imgR, 'rightConnector', this.drawnLog, captureCanvasOutputObj);
                    }


                }
                else{
                    for (var i=loopCounter; i>0; i--) {

                        if (type == 'left') {

                            /**
                             *  Wire #2
                             *  Left-Upper curve
                             */
                            x = this.canvasWireDim.coordLtX1 - 27;
                            y = this.canvasWireDim.coordLtY1;
                            w = this.canvasWireDim.coordLtW;
                            h = staticWireHeight;
                            if (i>1){
                                y = y - (i*diffHeight);
                            }
                            if (i == loopCounter)
                                y = y - diffHeight;

                            if (exception){
                                x = x + 32;
                                y = y - 5;
                            }

                            var dy = y - ((this.connectorImageHeight - this.canvasWireDim.height)/2);

                            canvasDrawImage(this, x, dy, imgL, 'leftConnector', this.drawnLog, captureCanvasOutputObj);



                            /**
                             *  Wire #2
                             *  Left-Bottom curve
                             */

                            x = canvasWireDim.coordLbX1 - 27;
                            y = canvasWireDim.coordLbY1;
                            w = canvasWireDim.coordLbW;
                            h = staticWireHeight;
                            if (i>1){
                                y = y+(i*diffHeight);
                            }
                            if (i == loopCounter)
                                y = y + diffHeight;
                            y = y + wireDiff+5;

                            if (exception){
                                x = x + 32;
                                y = y - 5;
                            }

                            var dy = y - ((this.connectorImageHeight - this.canvasWireDim.height)/2);

                            canvasDrawImage(this, x, dy, imgL, 'leftConnector', this.drawnLog, captureCanvasOutputObj);
                        }

                        if (type == 'right') {
                            //Right connectors
                            /**
                             *  Wire #3
                             *  Right-Upper curve
                             */
                            x = canvasWireDim.coordRtX1 + 27;
                            y = canvasWireDim.coordRtY1;
                            w = canvasWireDim.coordRtW;
                            h = staticWireHeight;
                            x = x+w;
                            if (i>1){
                                y = y - (i*diffHeight);
                            }
                            if (i == loopCounter)
                                y = y - diffHeight;

                            if(exception){
                                x = x - 32;
                                y = y - 5;
                            }

                            dy = y - ((this.connectorImageHeight - this.canvasWireDim.height)/2);

                            canvasDrawImage(this, x, dy, imgR, 'rightConnector', this.drawnLog, captureCanvasOutputObj);


                            /**
                             *  Wire #2
                             *  Right-Bottom curve
                             */
                            x = canvasWireDim.coordRbX1 + 27;
                            y = canvasWireDim.coordRbY1;
                            w = canvasWireDim.coordRbW;
                            h = staticWireHeight;
                            x = x+w;
                            if (i>1) {
                                y = y + (i*diffHeight);
                            }
                            if (i == loopCounter)
                                y = y + diffHeight;
                            y = y + wireDiff+5;

                            if(exception){
                                x = x - 32;
                                y = y - 5;
                            }

                            dy = y - ((this.connectorImageHeight - this.canvasWireDim.height)/2);

                            canvasDrawImage(this, x, dy, imgR, 'rightConnector', this.drawnLog, captureCanvasOutputObj);

                        }


                    }


                }

            }

        }


        /**
         * HACK:
         *   `coords = this.textConfig.left_2`: is a hack to use for the text drawing y coords
         *   If set to left or bottom, the height effects the original height params of the
         *   canvas obj.
         *   Matter of some R&D later!!
         */
        this.redrawText = function() {
            // Clearing the previous text
            var coords;
            coords = this.textConfig.left_2;
            if (this.wireType.type == 'fanout') {
                coords.y = coords.y + this.canvas.height/12;
            }

            // this.context.fillStyle = 'yellow';
            this.context.clearRect(0, coords.y - 40, this.canvasDim.width, 200);

            // Drawing text for all the positions
            for (var key in this.textLabels) {
                if (this.textLabels[key].length == 0)
                    continue;
                var textToDraw = '';
                for (var i=0; i<this.textLabels[key].length; i++) {
                    if ( this.textLabels[key][i] == undefined || this.textLabels[key][i].text == undefined)
                        continue;
                    if (this.textLabels[key][i].text.length > 0){
                        textToDraw += "  "+this.textLabels[key][i].text;
                    }
                }
                if (textToDraw.length > 0)
                    this.drawText({type: key, text: textToDraw});
            }

        }
        //Draw Text
        this.drawText = function(stringObj) {

            var coords;
            switch (stringObj.type){
                case 'left':
                    coords = this.textConfig.left;
                    break;
                case 'left-2':
                    coords = this.textConfig.left_2;
                    break;
                case 'left-3':
                    coords = this.textConfig.left_3;
                    break;
                case 'top':
                    coords = this.textConfig.top;
                    break;
                case 'right':
                    coords = this.textConfig.right;
                    break;
                case 'right-2':
                    coords = this.textConfig.right_2;
                    break;
                case 'right-3':
                    coords = this.textConfig.right_3;
                    break;
                case 'bottom':
                    coords = this.textConfig.bottom;
                    break;
                case 'top-left':
                    coords = this.textConfig.top_left;
                    break;

            }

            if (this.wireType.type == 'fanout') {
                coords.y = coords.y + this.canvas.height/12;
            }
            drawText(this, stringObj, coords);

        }

        this.drawLengthLine = function(CGuiObj) {
            drawLengthLine(this, CGuiObj);
        }

        this.checkIfExceptions = function(CGuiObj) {
            return checkIfExceptions(CGuiObj, this);
        }

        function checkIfExceptions(CGuiObj, CanvasToolObj) {

            for (var i=0; i<CGuiObj.selectedOptions.length; i++) {

                if (CGuiObj.selectedOptions[i].key.toLowerCase() == 'connector_a') {
                    if (CGuiObj.selectedOptions[i].value.cguiComponentName.toLowerCase().indexOf('e2000') !== -1)
                        CanvasToolObj.updateExceptions('e2000', true, 'left');
                    else
                        CanvasToolObj.updateExceptions('e2000', false, 'left');

                    if (CGuiObj.selectedOptions[i].value.cguiComponentName.toLowerCase().indexOf('uniboot') !== -1)
                        CanvasToolObj.updateExceptions('uniboot', true, 'left');
                    else
                        CanvasToolObj.updateExceptions('uniboot', false, 'left');

                    if (CGuiObj.selectedOptions[i].value.cguiComponentName.toLowerCase().indexOf('mtp') !== -1)
                        CanvasToolObj.updateExceptions('mtp', true, 'left');
                    else
                        CanvasToolObj.updateExceptions('mtp', false, 'left');

                }

                if (CGuiObj.selectedOptions[i].key.toLowerCase() == 'connector_b') {
                    if (CGuiObj.selectedOptions[i].value.cguiComponentName.toLowerCase().indexOf('e2000') !== -1)
                        CanvasToolObj.updateExceptions('e2000', true, 'right');
                    else
                        CanvasToolObj.updateExceptions('e2000', false, 'right');

                    if (CGuiObj.selectedOptions[i].value.cguiComponentName.toLowerCase().indexOf('pigtail') !== -1)
                        CanvasToolObj.updateExceptions('pigtail', true, 'right');
                    else
                        CanvasToolObj.updateExceptions('pigtail', false, 'right');

                    if (CGuiObj.selectedOptions[i].value.cguiComponentName.toLowerCase().indexOf('uniboot') !== -1)
                        CanvasToolObj.updateExceptions('uniboot', true, 'right');
                    else
                        CanvasToolObj.updateExceptions('uniboot', false, 'right');

                    if (CGuiObj.selectedOptions[i].value.cguiComponentName.toLowerCase().indexOf('mtp') !== -1)
                        CanvasToolObj.updateExceptions('mtp', true, 'right');
                    else
                        CanvasToolObj.updateExceptions('mtp', false, 'right');
                }

            }

            return CGuiObj.exceptions;
        }

        function drawLengthLine(CanvasToolObj, CGuiObj) {
            var currentWireTypeObj = CanvasToolObj.wireType;
            var h = 1;

            var measure = '';
            var userInput = '';
            for (var i=0; i<CGuiObj.selectedOptions.length; i++) {
                if (CGuiObj.selectedOptions[i].key == 'length') {
                    measure = CGuiObj.selectedOptions[i].value.unitSelected;
                    userInput = $.trim(CGuiObj.selectedOptions[i].value.userInput);
                }

            }

            // Check if exception connector is drawn, width to be adjusted
            var exceptionConnector =  CGuiObj.CanvasToolObj.exceptions;

            switch( currentWireTypeObj.type.toLowerCase() ) {

                case 'simplex':
                    var w = CanvasToolObj.canvasWireDim.width;
                    var y  = CanvasToolObj.canvasWireDim.coordY1;
                    var x = CanvasToolObj.drawnConnectorsDim.left.x;
                    w = w + CanvasToolObj.drawnConnectorsDim.left.w + CanvasToolObj.drawnConnectorsDim.right.w;


                    y = y - 100;

                    CanvasToolObj.context.clearRect(x, y-12, w, 32);

                    // Modify the wire starting and ending point if exception connector(i.e. e200) is drawn
                    if (exceptionConnector.e2000.left == true && exceptionConnector.e2000.right == true){
                        w = w - 64;
                    }
                    else if (exceptionConnector.e2000.left == true){
                        x = x;
                        if (exceptionConnector.pigtail.right == false)
                            w = w - 32;
                        else
                            w = w - 32;
                    }
                    else if (exceptionConnector.e2000.right == true){
                        w = w - 32;
                    }

                    CanvasToolObj.context.fillStyle = 'black';
                    CanvasToolObj.context.fillRect(x, y, w, h);

                    // Left and right edge indicators
                    CanvasToolObj.context.fillRect(x, y-10, 1, 20);
                    CanvasToolObj.context.fillRect(x+w, y-10, 1, 20);


                    var displayLength = userInput+ " " +measure;
                    drawTextWithBorder(CanvasToolObj, displayLength, 'black', 'white', 'black', "center", {x: x, y: y, w: w});

                    break;

                case 'duplex':
                    var w = CanvasToolObj.canvasWireDim.width;
                    var y  = CanvasToolObj.canvasWireDim.coordY1;
                    x = CanvasToolObj.drawnConnectorsDim.left.x;
                    w = w + CanvasToolObj.drawnConnectorsDim.left.w + CanvasToolObj.drawnConnectorsDim.right.w;

                    y = y - 100;

                    // Clear previous text
                    CanvasToolObj.context.clearRect(x, y-12, w, 32);

                    // Modify the wire starting and ending point if exception connector(i.e. e200) is drawn
                    if (exceptionConnector.e2000.left == true && exceptionConnector.e2000.right == true){
                        w = w - 64;
                    }
                    else if (exceptionConnector.e2000.left == true){
                        x = x;
                        if (exceptionConnector.pigtail.right == false)
                            w = w - 32;
                        else
                            w = w - 32;
                    }
                    else if (exceptionConnector.e2000.right == true){
                        w = w - 32;
                    }

                    CanvasToolObj.context.fillStyle = 'black';
                    CanvasToolObj.context.fillRect(x, y, w, h);
                    CanvasToolObj.context.textAlign = "center";

                    // Left and right edge indicators
                    CanvasToolObj.context.fillRect(x, y-10, 1, 20);
                    CanvasToolObj.context.fillRect(x+w, y-10, 1, 20);

                    var displayLength = userInput+ " " +measure;
                    CanvasToolObj.drawTextWithBorder(displayLength, 'black', 'white', 'black', "center", {x: x, y: y, w: w});
                    // CanvasToolObj.context.fillText(displayLength, (x+x+w)/2, y);
                    break;

                case 'fanout':
                    var w = CanvasToolObj.canvasWireDim.width;
                    var y  = CanvasToolObj.canvasWireDim.coordY1;
                    x = CanvasToolObj.drawnConnectorsDim.left.x;
                    w = w + CanvasToolObj.drawnConnectorsDim.left.w + CanvasToolObj.drawnConnectorsDim.right.w + (2*27);

                    y = y - 180;

                    CanvasToolObj.context.clearRect(x, y-12, w, 32);
                    // Modify the wire starting and ending point if exception connector(i.e. e200) is drawn
                    if (exceptionConnector.e2000.left == true && exceptionConnector.e2000.right == true){
                        w = w - 64;
                    }
                    else if (exceptionConnector.e2000.left == true){
                        x = x;
                        if (exceptionConnector.pigtail.right == false)
                            w = w - 32;
                        else
                            w = w - 32;
                    }
                    else if (exceptionConnector.e2000.right == true){
                        w = w - 32;
                    }

                    if (exceptionConnector.pigtail.right == true)
                        w = w - w/3 + 45;

                    CanvasToolObj.context.fillStyle = 'black';//CanvasToolObj.defaultWireColor;
                    CanvasToolObj.context.fillRect(x, y, w, h);
                    CanvasToolObj.context.textAlign = "center";

                    // Left and right edge indicators
                    CanvasToolObj.context.fillRect(x, y-10, 1, 20);
                    CanvasToolObj.context.fillRect(x+w, y-10, 1, 20);

                    var displayLength = userInput+ " " +measure;
                    CanvasToolObj.drawTextWithBorder(displayLength, 'black', 'white', 'black', "center", {x: x, y: y, w: w});
                    break;

            }

            return;
        }

        this.drawTextWithBorder = function(text, borderColor, bgColor, textColor, textAlign, textCoords) {
        	if (borderColor === undefined)
        		borderColor='black';
        	if (bgColor === undefined)
        		bgColor='white';
        	if (textColor === undefined)
        		textColor='black';
        	if (textAlign === undefined)
        		textAlign="center";
            drawTextWithBorder(this, text, borderColor, bgColor, textColor, textAlign, textCoords);
        }

        function drawTextWithBorder(CanvasToolObj, text, borderColor, bgColor, textColor, textAlign, textCoords) {
        	if (borderColor === undefined)
	        	borderColor='black';
	        if (bgColor === undefined)
	        	bgColor='white';
	        if (textColor === undefined)
	       		textColor='black';
	       	if (textAlign === undefined)
	       		textAlign="center";
            CanvasToolObj.context.font = CanvasToolObj.DEFAULT_FONT_SIZE+ ' '+CanvasToolObj.DEFAULT_FONT_FAMILY;
            CanvasToolObj.context.fillStyle = textColor;

            var txtWidth = CanvasToolObj.context.measureText(text).width;
            var fontHeight = parseInt(CanvasToolObj.context.font, CanvasToolObj.DEFAULT_FONT_SIZE);
            var padding = {
                left: 4,
                top: 2
            }
            // Adding border box to write text
            CanvasToolObj.context.lineWidth = 1;
            CanvasToolObj.context.fillStyle = borderColor;
            CanvasToolObj.context.strokeStyle = borderColor;
            CanvasToolObj.context.strokeRect( (textCoords.x+textCoords.x+textCoords.w)/2 - txtWidth/2 - padding.left, textCoords.y - fontHeight/2 - padding.top, txtWidth+ (2*padding.left), fontHeight + fontHeight/2 + padding.top);
            CanvasToolObj.context.fillStyle = bgColor;

            CanvasToolObj.context.fillRect( (textCoords.x+textCoords.x+textCoords.w)/2 - txtWidth/2 - padding.left, textCoords.y - fontHeight/2 - padding.top, txtWidth+ (2*padding.left), fontHeight + fontHeight/2 + padding.top);

            CanvasToolObj.context.fillStyle = textColor;
            CanvasToolObj.context.textAlign = textAlign;
            CanvasToolObj.context.fillText(text, (textCoords.x+textCoords.x+textCoords.w)/2, textCoords.y+fontHeight/2);
            return;
        }

        this.repaintCanvas = function(type) {
        	if (type === undefined)
        		type='undo';
            // Check if the current history index is the same as the length
            // of the history array(i.e. drawn wires/boots/connectors, sequentially)
            if (type == 'undo') {
                if (this.history.length == 0 || this.currentHistoryIndex == 0) {

                }
                this.currentHistoryIndex = this.currentHistoryIndex - 1;
            }
            if (type == 'redo') {
                if (this.history.length == 0 || this.currentHistoryIndex == this.history.length) {

                }
                this.currentHistoryIndex = this.currentHistoryIndex + 1;
            }
            if (this.history[this.currentHistoryIndex] === undefined)


            this.clearCanvas();
            canvasDrawImage(this, 0, 0, this.history[this.currentHistoryIndex], 'repaintCanvas', this.drawnLog);
            return;
        }

        this.updateDrawnConnectors = function(type, dim) {
            if (type == 'left') {
                this.drawnConnectorsDim.left = dim;
            }
            if (type == 'right') {
                this.drawnConnectorsDim.right = dim;
            }
        }

        this.clearCanvas = function(track) {
        	if (track === undefined)
        		track=true;
            clearCanvas(this, track);
        }

        this.recoverCanvasOutput = function(type) {
        	if (type === undefined)
        		type = false;
        	if (type == false)
                return false;
            var image=false;
            for (var i=0; i<this.history.length; i++) {
                if (this.history[i].key == type && image == false){
                    image = this.history[i].image;
                }
            }
            if (image == false)
                return false;
            recoverCanvasOutput(this, image);
        }

        this.roundRect = function() {
            return roundRect(this.context, 122, 122, 155, 89, 25);
        }



    }

    // For Setting up canvas
    function setCanvas(canvasSelector) {
        var canvas = null;
        var canvasSelectorType = canvasSelector.substring(0, 1);
        var canvasSelectorString = canvasSelector.substring(1);

        switch (canvasSelectorType){

            case '#':
                canvas = document.getElementById(canvasSelectorString);
                break;

            case '.':
                canvas = document.getElementsByClassName(canvasSelectorString)[0];
                break;

            default:
                canvas = document.getElementsByTagName(canvasSelector)[0];
                break;
        }
        return canvas;
    }

    // For setting up context from the canvas
    function setContext(canvas) {
        return canvas.getContext('2d');
    }

    function updateGui(guiKey, CGuiObj, reset) {
    	if (reset === undefined)
    		reset=false;
        switch (guiKey) {
            case 'price':
                var calcPrice = CGuiObj.CanvasToolObj.price;
                if (isNaN(parseFloat(calcPrice)) ||  parseFloat(calcPrice) == 0){
                    $("[data-canvas-gui='cartBtn']").attr('disabled', 'disabled');
                    calcPrice = '00.00';
                    // Display the quote btn & hide the price btn

                    $("[data-canvas-gui='cartBtn']").addClass('hidden');
                }
                else {
                    if (CGuiObj.CanvasToolObj.isPriceActive) {
                        $("[data-canvas-gui='cartBtn']").removeAttr('disabled');
                        $("[data-canvas-gui='cartBtn']").removeClass('hidden');
                    }
                    else {
                        $("[data-canvas-gui='cartBtn']").addClass('hidden');
                        $("[data-canvas-gui='cartBtn']").attr('disabled', 'disabled');
                    }
                }
                $('[data-canvas-gui="price"]').html(calcPrice);
                break;

            case 'part-number':
                $('[data-canvas-gui="part-number"]').html(CGuiObj.CanvasToolObj.partNumber);
                if (reset)
                    $('[data-canvas-gui="part-number"]').parent().addClass('hidden');
                else
                    $('[data-canvas-gui="part-number"]').parent().removeClass('hidden');
                // if (reset == true)
                break;

            case 'print':
                if (CGuiObj.CanvasToolObj.isLastStepDone)
                    $('[data-canvas-gui="printCanvasBtn"]').removeClass('hidden');
                else
                    $('[data-canvas-gui="printCanvasBtn"]').addClass('hidden');
                if (reset == true)
                    $('[data-canvas-gui="printCanvasBtn"]').addClass('hidden');
                // console.log("Hide print btn", reset);
                break;

            case 'resetBtn':
                if (reset == true)
                    $('[data-canvas-gui="resetCanvasBtn"]').addClass('hidden');
                else
                    $('[data-canvas-gui="resetCanvasBtn"]').removeClass('hidden');
                break;

            case 'length':
                if (reset == true) {
                    $('input#inputField')
                        .attr('placeholder', '0')
                        .val('');
                }
                $('ol#steps-customs')
                    .find('li[data-config-name="length"]')
                    .find('ul.sub-content select')
                    .prop('selectedIndex', 0);
                break;

            case 'resetSpecialOrderBtn':
                if (reset){
                    $("[data-canvas-gui='specialOrderPopupBtn']").addClass('hidden');
                    $("[data-canvas-gui='specialOrderPopupBtn']").attr('disabled', 'disabled');
                    return;
                }
                if (CGuiObj.CanvasToolObj.isPriceActive) {
                    $("[data-canvas-gui='specialOrderPopupBtn']").attr('disabled', 'disabled');
                    $("[data-canvas-gui='specialOrderPopupBtn']").addClass('hidden');
                }
                else
                {
                    $("[data-canvas-gui='specialOrderPopupBtn']").removeAttr('disabled');
                    $("[data-canvas-gui='specialOrderPopupBtn']").removeClass('hidden');
                }
                break;

            /*case 'userDiscount':
                $('[data-canvas-gui="user_discount"]').html(CGuiObj.CanvasToolObj.user_discount);
                $("[data-canvas-gui='user_discount']").parent().addClass('hidden');
                if (CGuiObj.CanvasToolObj.isPriceActive) {
                    $("[data-canvas-gui='user_discount']").parent().removeClass('hidden');
                }
                break;*/
            default:
                break;
        }
    }

    // Utility method to clear canvas
    function clearCanvas(GlobalCanvasObj, track) {
    	if (track === undefined)
    		track = true;
        //Clearing the drawnLog
        if (track == true) {
            GlobalCanvasObj.drawnLog.stack.length = 0;
            GlobalCanvasObj.drawnLog.lastDrawn = {};
        }
        GlobalCanvasObj.context.clearRect(0, 0, GlobalCanvasObj.canvasDim.width, GlobalCanvasObj.canvasDim.height);

    }

    /**
     * Calculating Weight
     */
    function updateWeight(CGuiObj) {

        var bootCountPerSide = 0;
        var connectorCount = 0;
        switch (CGuiObj.CanvasToolObj.wireType.type) {
            // #1 Simplex
            case 'simplex':
                bootCountPerSide = 1;
                break;

            // #2 Duplex
            case 'duplex':
                bootCountPerSide = 2;
                break;

            // #3 Fanouts
            case 'fanout':
                bootCountPerSide = CGuiObj.CanvasToolObj.wireType.wiresCount;
                break;

            default:
                bootCountPerSide = 1;
                break;
        }

        var weight = 0;
        for (var i=0; i<CGuiObj.selectedOptions.length; i++) {
            if (CGuiObj.selectedOptions[i].key.toLowerCase() == 'boot_type') {
                var currWeight = (2*bootCountPerSide) * (parseFloat(CGuiObj.selectedOptions[i].value.cguiComponentWeight));

                if (CGuiObj.CanvasToolObj.exceptions.pigtail.right == true && CGuiObj.CanvasToolObj.exceptions.uniboot.left == true){
                    var currWeight = parseFloat(CGuiObj.selectedOptions[i].value.cguiComponentWeight);
                }
                else if (CGuiObj.CanvasToolObj.exceptions.pigtail.right == true && CGuiObj.CanvasToolObj.exceptions.uniboot.left != true) {
                    var currWeight = bootCountPerSide * parseFloat(CGuiObj.selectedOptions[i].value.cguiComponentWeight);
                }
                else if (CGuiObj.CanvasToolObj.exceptions.uniboot.left == true && CGuiObj.CanvasToolObj.exceptions.uniboot.right == true) {
                    var currWeight = 2*parseFloat(CGuiObj.selectedOptions[i].value.cguiComponentWeight);
                }
                else if (CGuiObj.CanvasToolObj.exceptions.uniboot.left == true || CGuiObj.CanvasToolObj.exceptions.uniboot.right == true) {
                    var currWeight = parseFloat(CGuiObj.selectedOptions[i].value.cguiComponentWeight) + (bootCountPerSide) * (parseFloat(CGuiObj.selectedOptions[i].value));
                }


            }
            else if (CGuiObj.selectedOptions[i].key.toLowerCase() == 'connector_a') {
                var currWeight = bootCountPerSide * parseFloat(CGuiObj.selectedOptions[i].value.cguiComponentWeight);

                // Override the price incase of uniboot
                if (CGuiObj.CanvasToolObj.exceptions.uniboot.left == true) {
                    var currWeight = parseFloat(CGuiObj.selectedOptions[i].value.cguiComponentWeight);
                }

            }
            else if (CGuiObj.selectedOptions[i].key.toLowerCase() == 'connector_b') {
                if (CGuiObj.CanvasToolObj.exceptions.pigtail.right == true)
                    var currWeight = parseFloat(CGuiObj.selectedOptions[i].value.cguiComponentWeight);
                else
                    var currWeight = bootCountPerSide * parseFloat(CGuiObj.selectedOptions[i].value.cguiComponentWeight);

                // Override the price incase of uniboot
                if (CGuiObj.CanvasToolObj.exceptions.uniboot.right == true) {
                    var currWeight = parseFloat(CGuiObj.selectedOptions[i].value.cguiComponentWeight);
                }

            }
            else{
                var currWeight = parseFloat(CGuiObj.selectedOptions[i].value.cguiComponentWeight);
            }

            if (typeof(currWeight) === undefined || isNaN(currWeight) ) {
                // console.log("Skipping weight:",  currWeight, " for", CGuiObj.selectedOptions[i].key.toLowerCase());
                currWeight = 0;
                // continue;
            }
            // console.log("Adding weight:",  currWeight, " for", CGuiObj.selectedOptions[i].key.toLowerCase());

            weight += currWeight;

            // console.log("Total Weight after adjustment", weight);

        }

        // console.log("Weight ==", weight, " formatted weight", weight.toFixed(2).toString());
        return weight.toFixed(2).toString();

    }

    // Price for the canvas product generated
    function updatePrice(CGuiObj) {
        var price = undefined;
        var cablePriceObj = {};

        var cablePrice = 0;
        var unitSelected = 'I';
        var cableLength = 0;
        // Calculate the price for wire
        var uniqueCableId = {
            glassType: '',
            jacketType: '',
            fiberCount: '',

            // For Copper Products
            cableType: '',
            cableColor: '',

            misc: [],
            // Hack for pricing for cassetes group
            all: []
        }

        // Selection required for length calcualtion incase of Copper Group requires #2 fields
        var minLengthForWireCombo = 3;
        if (CGuiObj.CanvasToolObj.activeGroup.indexOf('copper') !== -1){
            minLengthForWireCombo = 2;
        }
        if (CGuiObj.selectedOptions.length < minLengthForWireCombo)
            return price;

        for (var i=0; i<CGuiObj.selectedOptions.length; i++) {

            if (CGuiObj.selectedOptions[i].value.cguiComponentPrice != '' &&  !isNaN(CGuiObj.selectedOptions[i].value.cguiComponentPrice))
                    uniqueCableId.all.push({key: CGuiObj.selectedOptions[i].key, value: CGuiObj.selectedOptions[i].value.cguiComponentPrice});

            var currentType = CGuiObj.selectedOptions[i].key;
            switch (currentType){
                case 'glass_type':
                    uniqueCableId.glassType = CGuiObj.selectedOptions[i].value.cguiComponentPartNumber;
                    break;

                case 'jacket_type':
                    uniqueCableId.jacketType = CGuiObj.selectedOptions[i].value.cguiComponentPartNumber;
                    break;

                case 'fiber_count':
                    uniqueCableId.fiberCount = CGuiObj.selectedOptions[i].value.cguiComponentPartNumber;
                    break;

                case 'cable_type':
                    uniqueCableId.cableType = CGuiObj.selectedOptions[i].value.cguiComponentPartNumber;
                    break;

                case 'cable_color':
                    uniqueCableId.cableColor = CGuiObj.selectedOptions[i].value.cguiComponentPartNumber;
                    break;

                case 'length':
                    unitSelected = CGuiObj.selectedOptions[i].value.unitPartNumberSelected;
                    cableLength = CGuiObj.selectedOptions[i].value.userInput;
                    break;

                default:
                    if (CGuiObj.CanvasToolObj.activeGroup == 'mtp/mpo cassettes' && CGuiObj.selectedOptions[0].value.cguiComponentPrice == '')
                    {                        
                        CGuiObj.CanvasToolObj.setPriceStatus(false);
                        return price;
                    }                    
                    if (CGuiObj.selectedOptions[i].value.cguiComponentPrice != '' &&  !isNaN(CGuiObj.selectedOptions[i].value.cguiComponentPrice))
                        uniqueCableId.misc.push({key: CGuiObj.selectedOptions[i].key, value: CGuiObj.selectedOptions[i].value.cguiComponentPrice});
                    break;
            }
        }


        var definedDBConditions = CGuiObj.models.conditions.db;
        // Get the current cable id combination(#1 & #2) to work with for DBConditions OBJ
        if (CGuiObj.selectedOptions.length == 0)
            return {};

        // Extracting the wire price
        var currentCableId;

        // Processing the wire color by comparing selections with the predefined db conditions
        // Cable ID incase of Copper Products uses cableType and cableColor
        if (CGuiObj.CanvasToolObj.activeGroup.indexOf('copper') !== -1)
            currentCableId = uniqueCableId.cableType +''+ uniqueCableId.cableColor;
        else
            currentCableId = uniqueCableId.glassType +''+ uniqueCableId.jacketType+''+ uniqueCableId.fiberCount;


        for (var i=0; i<CGuiObj.models.conditions.db.length; i++) {
            if (CGuiObj.models.conditions.db[i].cable_id == currentCableId) {
                cablePriceObj = CGuiObj.models.conditions.db[i].price;
                break;
            }
        }

        // console.log("Calculating price");

        // Get the selection( F|M|I)
        if (unitSelected.toString().toLowerCase() == 'f') {
            price = cablePriceObj.f;
        }

        if (unitSelected.toString().toLowerCase() == 'i') {
            price = cablePriceObj.i;
        }

        if (unitSelected.toString().toLowerCase() == 'm') {
            price = cablePriceObj.m;
        }

        // console.log("Calculating initial price for wire combo", parseFloat(price) , parseFloat(cableLength) );

        price = parseFloat(price) * parseFloat(cableLength);
        // console.log("Price until cable and measure calcualtion", price);

        // Manipulate the price, ignore wires-combo incase of cassettes
        var ignoreWiresCombo = false;
        if ( $('ol#steps-customs li.parent-element').eq(0).data('group-name') !== undefined && $('ol#steps-customs li.parent-element').eq(0).data('group-name').toLowerCase().indexOf('cassette') !== -1)
            ignoreWiresCombo = true;

        if ((isNaN(price) || price <= 0 ) && !ignoreWiresCombo) {
            CGuiObj.CanvasToolObj.setPriceStatus(false);
            return price.toFixed(2).toString();

        }
        else {
            CGuiObj.CanvasToolObj.setPriceStatus(true);
        }
        // Now add further pricing, on the basis of all other components selections
        // Find boots count
        // #3 Cases

        var bootCountPerSide = 0;
        var connectorCount = 0;
        switch (CGuiObj.CanvasToolObj.wireType.type) {
            // #1 Simplex
            case 'simplex':
                bootCountPerSide = 1;
                break;

            // #2 Duplex
            case 'duplex':
                bootCountPerSide = 2;
                break;

            // #3 Fanouts
            case 'fanout':
                bootCountPerSide = CGuiObj.CanvasToolObj.wireType.wiresCount;
                break;

            default:
                bootCountPerSide = 1;
                break;
        }

        if (isNaN(price) || price === undefined)
            price = 0;



        var priceSetToWorkWith = uniqueCableId.misc;
        if (ignoreWiresCombo)
            priceSetToWorkWith = uniqueCableId.all;

        for (var i=0; i<priceSetToWorkWith.length; i++) {
            if (priceSetToWorkWith[i].key.toLowerCase() == 'boot_type') {
                var currPrice = (2*bootCountPerSide) * (parseFloat(priceSetToWorkWith[i].value));

                if (CGuiObj.CanvasToolObj.exceptions.pigtail.right == true && CGuiObj.CanvasToolObj.exceptions.uniboot.left == true){
                    var currPrice = parseFloat(priceSetToWorkWith[i].value);
                }
                else if (CGuiObj.CanvasToolObj.exceptions.pigtail.right == true && CGuiObj.CanvasToolObj.exceptions.uniboot.left != true) {
                    var currPrice = bootCountPerSide * parseFloat(priceSetToWorkWith[i].value);
                }
                else if (CGuiObj.CanvasToolObj.exceptions.uniboot.left == true && CGuiObj.CanvasToolObj.exceptions.uniboot.right == true) {
                    var currPrice = 2*parseFloat(priceSetToWorkWith[i].value);
                }
                else if (CGuiObj.CanvasToolObj.exceptions.uniboot.left == true || CGuiObj.CanvasToolObj.exceptions.uniboot.right == true) {
                    var currPrice = parseFloat(priceSetToWorkWith[i].value) + (bootCountPerSide) * (parseFloat(priceSetToWorkWith[i].value));
                }


            }
            else if (priceSetToWorkWith[i].key.toLowerCase() == 'connector_a') {
                var currPrice = bootCountPerSide * parseFloat(priceSetToWorkWith[i].value);

                // Override the price incase of uniboot
                if (CGuiObj.CanvasToolObj.exceptions.uniboot.left == true) {
                    var currPrice = parseFloat(priceSetToWorkWith[i].value);
                }

            }
            else if (priceSetToWorkWith[i].key.toLowerCase() == 'connector_b') {
                if (CGuiObj.CanvasToolObj.exceptions.pigtail.right == true)
                    var currPrice = parseFloat(priceSetToWorkWith[i].value);
                else
                    var currPrice = bootCountPerSide * parseFloat(priceSetToWorkWith[i].value);

                // Override the price incase of uniboot
                if (CGuiObj.CanvasToolObj.exceptions.uniboot.right == true) {
                    var currPrice = parseFloat(priceSetToWorkWith[i].value);
                }

            }
            else{
                var currPrice = parseFloat(priceSetToWorkWith[i].value);
            }

            if (typeof(currPrice) === undefined || isNaN(currPrice) ) {
                // console.log("Skipping price:",  currPrice, " for", priceSetToWorkWith[i].key.toLowerCase());
                currPrice = 0;
                // continue;
            }
            // console.log("Adding price:",  currPrice, " for", priceSetToWorkWith[i].key.toLowerCase());

            price += currPrice;

            // console.log("Total Price after adjustment", price);

        }

        // console.log("Price ==", price, " formatted price", price.toFixed(2).toString());
        // Applying discount
        CGuiObj.CanvasToolObj.updateUserDiscount( (parseFloat(price) * parseFloat(CGuiObj.CanvasToolObj.USER_DISCOUNT_PERCENTAGE) )/100);
        // console.log("User discount", this.user_discount);
        var user_discount = 0;
        if (parseFloat(CGuiObj.CanvasToolObj.user_discount) > 0)
            user_discount = CGuiObj.CanvasToolObj.user_discount;
        // console.log("User discount before moving further", CGuiObj.CanvasToolObj.user_discount);

        price = price - user_discount;
        // console.log("Price after applying discount", price, " formatted price", price.toFixed(2).toString());
        return price.toFixed(2).toString();
    }

    function addZeroes( num ) {

        var value = Number(num);
        var res = num.split(".");
        if(num.indexOf('.') === -1) {
            value = value.toFixed(2);
            num = value.toString();
        } else if (res[1].length < 3) {
            value = value.toFixed(2);
            num = value.toString();
        }

        return num
    }

    //Utility method to extend defaults with user options
    function extendDefaults(source, properties) {
        var property;
        for (property in properties) {
            if (properties.hasOwnProperty(property)) {
                source[property] = properties[property];
            }
        }
        return source;
    }

    //Wire functions
    // wiresCount is for fanouts
    function setWireType(canvas, wireType, wiresCount) {
    	if (wireType === undefined)
    		wireType='simplex';
    	if (wiresCount === undefined)
    		wiresCount=0;
        canvas.wireType = {type: wireType, wiresCount: wiresCount};
        return;
    }

    //Basic image drawn of a wire
    // drawnFigure: ** Param must be an array from drawnPipes/drawnBoots/drawnConnectors
    function drawWire(CanvasTool)
    {
        var wireStroke = CanvasTool.wireStroke;
        var type = CanvasTool.wireType.type;
        var wiresCount = CanvasTool.wireType.wiresCount;
        color_for_wire = CanvasTool.defaultWireColor;
        color_for_stroke = CanvasTool.defaultStrokeColor;
        canvas = CanvasTool.canvas;
        context = CanvasTool.context;
        canvasWireDim = CanvasTool.canvasWireDim;
        globalDrawnLogObj = CanvasTool.drawnLog;

        var wireColor = color_for_wire;
        var fanoutColor = color_for_wire;

        // if (CanvasTool.activeConds.staticConds !== undefined && CanvasTool.activeConds.staticConds.cable !== undefined && CanvasTool.activeConds.staticConds.cable.key !== ''){
        //     if (CanvasTool.activeConds.staticConds.cable.cableColor.length != 0)
        //         wireColor = CanvasTool.activeConds.staticConds.cable.cableColor;
        //     if (CanvasTool.activeConds.staticConds.cable.fanoutColor.length != 0)
        //         fanoutColor = CanvasTool.activeConds.staticConds.cable.fanoutColor;
        // }

        if (type == 'simplex') {

            logDrawn(globalDrawnLogObj, {x1: canvasWireDim.coordX1, y1: canvasWireDim.coordY1, w: canvasWireDim.width, h: canvasWireDim.height}, 'wire');

            var x = canvasWireDim.coordX1;
            var y = canvasWireDim.coordY1;
            var w = canvasWireDim.width;
            var h = canvasWireDim.height;

            // context.lineWidth = 5;
            context.lineWidth = 1.5;
            context.strokeStyle = color_for_stroke;
            // context.stroke();

            context.fillStyle = wireColor;
            context.fillRect(x, y, w, h);
            context.strokeRect(x, y, w, h);

        //Do some fancy stuff
        } else if (type == 'duplex') {

            /** Wire #1 (main wire)
             *  2 rects, one to depict the wire edge & other for the actual color
             *  The base of the wire i.e.
             *          ----------
             *          ----------
             */

            var staticWireHeight = CanvasTool.canvasWireDim.height;

            var x = canvasWireDim.coordX1;
            var y = canvasWireDim.coordY1;
            var w = canvasWireDim.coordW;
            var h = staticWireHeight;// THis is the static height

            // For edge #1
            context.fillStyle = color_for_stroke;
            context.fillRect(x, y, w, h);
            // For actual wire color
            context.fillStyle = wireColor;
            context.fillRect(x-2, y+wireStroke, w+4, h-(2*wireStroke));


            drawPipe(CanvasTool, x, y, 'topLeft', wireColor);
            drawPipe(CanvasTool, x+w, y, 'topRight', wireColor);


            /**
             *  Wire #2
             *  Left-Upper curve
             */

            context.fillStyle = color_for_stroke;

            x = canvasWireDim.coordLtX1;
            y = canvasWireDim.coordLtY1;
            w = canvasWireDim.coordLtW;
            h = staticWireHeight;

            context.fillStyle = color_for_stroke;
            context.fillRect(x, y, w, h);
            context.fillStyle = wireColor;
            context.fillRect(x, y+wireStroke, w , h-(2*wireStroke));

            drawPipe(CanvasTool, x+w, y, 'bottomRight', wireColor);

            /**
             *  Wire #3
             *  Right-Upper curve
             */
            context.fillStyle = color_for_stroke;

            x = canvasWireDim.coordRtX1;
            y = canvasWireDim.coordRtY1;
            w = canvasWireDim.coordRtW;
            h = staticWireHeight;


            context.fillStyle = color_for_stroke;
            context.fillRect(x, y, w, h);
            context.fillStyle = wireColor;
            context.fillRect(x, y+wireStroke, w, h-(2*wireStroke));

            drawPipe(CanvasTool, x, y, 'bottomLeft', wireColor);


            /**
             *  Wire #2
             *  Left-Bottom curven drawWire
             */

            var wireDiff = 11;
            var x = canvasWireDim.coordX1;
            var y = canvasWireDim.coordY1;
            var w = canvasWireDim.coordW;
            var h = staticWireHeight;// THis is the static height

            // For edge #2
            y = y + wireDiff;
            context.fillStyle = color_for_stroke;
            context.fillRect(x, y, w, h);

            // For actual wire color
            context.fillStyle = wireColor;
            context.fillRect(x-2, y+wireStroke, w+4, h-(2*wireStroke));

            drawPipe(CanvasTool, x, y, 'bottomLeft', wireColor);
            drawPipe(CanvasTool, x+w, y, 'bottomRight', wireColor);

            /**
             *  Wire #2
             *  Left-Bottom curve
             */
            context.fillStyle = color_for_stroke;

            x = canvasWireDim.coordLbX1;
            y = canvasWireDim.coordLbY1;
            w = canvasWireDim.coordLbW;
            h = staticWireHeight;
            y = y + wireDiff;

            context.fillStyle = color_for_stroke;
            context.fillRect(x, y, w, h);
            context.fillStyle = wireColor;
            context.fillRect(x, y+wireStroke, w , h-(2*wireStroke));

            drawPipe(CanvasTool, x+w, y, 'topRight', wireColor);

            /**
             *  Wire #2
             *  Right-Bottom curve
             */
            context.fillStyle = color_for_stroke;

            x = canvasWireDim.coordRbX1;
            y = canvasWireDim.coordRbY1;
            w = canvasWireDim.coordRbW;
            h = staticWireHeight;
            y = y + wireDiff;

            context.fillStyle = color_for_stroke;
            context.fillRect(x, y, w, h);
            context.fillStyle = wireColor;
            context.fillRect(x, y+wireStroke, w , h-(2*wireStroke));

            drawPipe(CanvasTool, x, y, 'topLeft', wireColor);

            return;


        } else if (type == 'fanout') {

            var multiColorSet = CanvasTool.exceptions.fanoutMultiColors;

            var multiColorFlag = false;
            if (fanoutColor.toLowerCase() == 'multi') {
                multiColorFlag = true;
            }

            var wiresCount = CanvasTool.wireType.wiresCount;

            var iterationForMultiTop = 0;
            var iterationForMultiBottom = 5;
            var loopCounter = 0;
            switch(parseInt(wiresCount)) {

                case 4:
                    loopCounter = 2;
                    iterationForMultiBottom = 4;
                    break;

                case 6:
                    loopCounter = 3;
                    break;

                default:
                    loopCounter = 3;
                    break;
            }

            var staticWireHeight = CanvasTool.canvasWireDim.height;
            var diffHeight = 20;
            var diffWithinWire = 4;
            var diffWireBetween = 16;
            var wireMultiplier = 2;

            var numberToDisplayTop = 1;
            var numberToDisplayBottom = loopCounter+1;

            var jacketCoords = {
                x: canvasWireDim.coordX1 - 25 - (27),
                y: canvasWireDim.coordY1,
                w: canvasWireDim.coordW + 50 + (2*27),
                h: staticWireHeight*loopCounter
            };

            for (var i=loopCounter; i>0; i--) {

                // Top Part generation
                var x = canvasWireDim.coordX1 - 27;
                var y = canvasWireDim.coordY1;
                var w = canvasWireDim.coordW + (2*27);
                var h = staticWireHeight;// THis is the static height
                x = x - 54;
                if (i>1){
                    y = y + (i*diffWithinWire);
                }
                /**
                 * Extended edge, vertical wires
                 */
                // For edge #1

                        fx = x-25;

                        fy = y-(i*wireMultiplier*diffHeight)+13;

                        fw = 10;
                        fh = i*diffHeight+5;

                        if (loopCounter == 2 && i == 2) {
                            fy = fy - 18;
                            fh = fh + 30;
                        }

                        if (loopCounter == 2 && i == 1) {
                            fy = fy - 18;
                            fh = 0;
                        }

                        //Left extra edge
                        context.fillStyle = color_for_stroke;
                        context.fillRect(fx, fy, fw, fh);
                        // For actual wire color
                        if (multiColorFlag)
                            context.fillStyle = multiColorSet[iterationForMultiTop];
                        else
                            context.fillStyle = fanoutColor;

                        context.fillRect(fx+wireStroke, fy, fw-(2*wireStroke), fh);

                        fx = x + w+15;
                        // Right extra edge

                        fx = fx + 54 + 54;
                        context.fillStyle = color_for_stroke;
                        context.fillRect(fx, fy, fw, fh);
                        // For actual wire color

                        if (multiColorFlag)
                            context.fillStyle = multiColorSet[iterationForMultiTop];
                        else
                            context.fillStyle = fanoutColor;

                        context.fillRect(fx+wireStroke, fy, fw-(2*wireStroke), fh);

                // For edge #1
                w = w + 54 + 54;
                context.fillStyle = color_for_stroke;
                context.fillRect(x, y, w, h);

                // For actual wire color
                if (multiColorFlag)
                    context.fillStyle = multiColorSet[iterationForMultiTop];
                else
                    context.fillStyle = fanoutColor;

                context.fillRect(x, y+wireStroke, w, h-(2*wireStroke));

                if (multiColorFlag) {
                    drawPipe(CanvasTool, x, y, 'topLeft', multiColorSet[iterationForMultiTop]);
                    drawPipe(CanvasTool, x+w, y, 'topRight', multiColorSet[iterationForMultiTop]);
                }
                else {
                    drawPipe(CanvasTool, x, y, 'topLeft', fanoutColor);
                    drawPipe(CanvasTool, x+w, y, 'topRight', fanoutColor);
                }

                /**
                 *  Wire #2
                 *  Left-Upper curve
                 */
                context.fillStyle = color_for_stroke;

                x = canvasWireDim.coordLtX1 - 27;
                y = canvasWireDim.coordLtY1;
                w = canvasWireDim.coordLtW;

                w = w - 54;
                h = staticWireHeight;
                if (i>1){
                    y = y - (i*diffHeight);
                }
                if (i == loopCounter)
                    y = y - diffHeight;
                context.fillStyle = color_for_stroke;
                context.fillRect(x, y, w, h);

                if (multiColorFlag)
                    context.fillStyle = multiColorSet[iterationForMultiTop];
                else
                    context.fillStyle = fanoutColor;

                context.fillRect(x, y+wireStroke, w , h-(2*wireStroke));

                if (multiColorFlag)
                    drawPipe(CanvasTool, x+w, y, 'bottomRight', multiColorSet[iterationForMultiTop]);
                else
                    drawPipe(CanvasTool, x+w, y, 'bottomRight', fanoutColor);

                // Top Left numbers
                var text = numberToDisplayTop.toString();
                CanvasTool.drawTextWithBorder(text, 'black', 'white', 'black', "center", {x: x+w-(60), y: y+5, w: w});

                /**
                 *  Wire #3
                 *  Right-Upper curve
                 */
                context.fillStyle = color_for_stroke;

                x = canvasWireDim.coordRtX1 + 27;
                y = canvasWireDim.coordRtY1;
                w = canvasWireDim.coordRtW;
                h = staticWireHeight;

                x = x + 54;
                w = w - 54;
                if (i>1){
                    y = y - (i*diffHeight);
                }
                if (i == loopCounter)
                    y = y - diffHeight;

                context.fillStyle = color_for_stroke;
                context.fillRect(x, y, w, h);

                if (multiColorFlag)
                    context.fillStyle = multiColorSet[iterationForMultiTop];
                else
                    context.fillStyle = fanoutColor;

                context.fillRect(x, y+wireStroke, w, h-(2*wireStroke));

                if (multiColorFlag)
                    drawPipe(CanvasTool, x, y, 'bottomLeft', multiColorSet[iterationForMultiTop]);
                else
                    drawPipe(CanvasTool, x, y, 'bottomLeft', fanoutColor);

                // Top Right numbers
                // Draw labelled text only in case of fanout-6
                // if (loopCounter == 3){
                CanvasTool.drawTextWithBorder(text, 'black', 'white', 'black', "center", {x: x-w+(60), y: y+5, w: w});
                numberToDisplayTop++;
                // }


                // Bottom Part genneration
                var wireDiff = 15;
                var x = canvasWireDim.coordX1 - 27;
                var y = canvasWireDim.coordY1;
                var w = canvasWireDim.coordW + (2*27);
                var h = staticWireHeight;// THis is the static height
                y = y + diffWireBetween;
                x = x - 54;
                w = w + 54 + 54;
                if (i>1){
                    y = y - (diffWithinWire);
                }


                /**
                 * Extended edge
                 */
                // For edge #1
                if (i>0){
                    // if(i == loopCounter){
                        fx = x-25;
                        // dy = y-(i*diffHeight)-15;
                        fy = y-(18)+(wireMultiplier*diffHeight);//+(i*diffHeight)+2;//(i*diffHeight)+diffWithinWire+35;
                        fw = 10;
                        fh = (i*diffHeight*wireMultiplier)-28;//+(20*wireMultiplier);

                        /*
                            fy = y-(i*diffHeight)-30;
                            fy = fy-diffHeight;
                            fw = 10;
                            fh = i*diffHeight+30;
                        */

                        /*fx = x-25;
                        fy = y-(i*diffHeight)-30;
                        fy = fy+diffHeight;
                        fw = 10;
                        fh = i*diffHeight+40;*/

                        if (loopCounter == 2 && i == 1) {
                            fy = 0;//fy + 18;
                            fh = 0;//20;
                        }

                        if (loopCounter == 2 && i == 2) {
                            fy = fy + 12;
                            fh = fh + 10;
                        }

                        // Hack for 3-fanout, bottom right edge was generated radomly
                        if (loopCounter == 3 && i == 1) {
                            fh = 0;
                        }


                        //Left extra edge
                        context.fillStyle = color_for_stroke;
                        context.fillRect(fx, fy, fw, fh-1);
                        // For actual wire color
                        if (multiColorFlag)
                            context.fillStyle = multiColorSet[iterationForMultiBottom];
                        else
                            context.fillStyle = fanoutColor;

                        context.fillRect(fx+wireStroke, fy, fw-(2*wireStroke), fh);

                        fx = x + w+15;
                        // Right extra edge
                        context.fillStyle = color_for_stroke;
                        context.fillRect(fx, fy, fw, fh-1);

                        // For actual wire color
                        if (multiColorFlag)
                            context.fillStyle = multiColorSet[iterationForMultiBottom];
                        else
                            context.fillStyle = fanoutColor;

                        context.fillRect(fx+wireStroke, fy, fw-(2*wireStroke), fh);
                    // }
                }

                // For edge #2
                // y = y + wireDiff;


                context.fillStyle = color_for_stroke;
                context.fillRect(x, y, w, h);

                // For actual wire color
                if (multiColorFlag)
                    context.fillStyle = multiColorSet[iterationForMultiBottom];
                else
                    context.fillStyle = fanoutColor;

                context.fillRect(x, y+wireStroke, w, h-(2*wireStroke));

                if (multiColorFlag) {
                    drawPipe(CanvasTool, x, y, 'bottomLeft', multiColorSet[iterationForMultiBottom]);
                    drawPipe(CanvasTool, x+w, y, 'bottomRight', multiColorSet[iterationForMultiBottom]);
                }
                else {
                    drawPipe(CanvasTool, x, y, 'bottomLeft', fanoutColor);
                    drawPipe(CanvasTool, x+w, y, 'bottomRight', fanoutColor);
                }

                /**
                 *  Wire #2
                 *  Left-Bottom curve
                 */
                context.fillStyle = color_for_stroke;

                x = canvasWireDim.coordLbX1 - 27;
                y = canvasWireDim.coordLbY1;
                w = canvasWireDim.coordLbW;
                h = staticWireHeight;

                w = w - 54;
                if (i>1){
                    y = y+(i*diffHeight);
                }
                if (i == loopCounter)
                    y = y + diffHeight;
                y = y + wireDiff;

                context.fillStyle = color_for_stroke;
                context.fillRect(x, y, w, h);

                if (multiColorFlag)
                    context.fillStyle = multiColorSet[iterationForMultiBottom];
                else
                    context.fillStyle = fanoutColor;

                context.fillRect(x, y+wireStroke, w , h-(2*wireStroke));

                if (multiColorFlag)
                    drawPipe(CanvasTool, x+w, y, 'topRight', multiColorSet[iterationForMultiBottom]);
                else
                    drawPipe(CanvasTool, x+w, y, 'topRight', fanoutColor);

                // Drawing numbers only if fanout-6
                if (loopCounter == 3){
                    // Bottom Left numbers
                    var fanoutNumberLabel = '';
                    // If there are 6 fanouts in all
                    if ( i == 1 )
                        fanoutNumberLabel = '4';

                    if (i == 2) {
                        if ( parseInt(wiresCount)-1 == 5 )
                            fanoutNumberLabel = '5';
                        else
                            fanoutNumberLabel = '..';
                    }
                    // Incase of last loop, display 6
                    if (i == 3) {
                        fanoutNumberLabel = wiresCount.toString();
                    }
                }
                else{
                    if ( i == 1 )
                        fanoutNumberLabel = '3';

                    if (i == 2)
                        fanoutNumberLabel = '4';

                }
                CanvasTool.drawTextWithBorder(fanoutNumberLabel, 'black', 'white', 'black', "center", {x: x+w-60, y: y+5, w: w});
                /**
                 *  Wire #2
                 *  Right-Bottom curve
                 */
                context.fillStyle = color_for_stroke;

                x = canvasWireDim.coordRbX1 + 27;
                y = canvasWireDim.coordRbY1;
                w = canvasWireDim.coordRbW;
                h = staticWireHeight;

                x = x + 54;
                w = w - 54;

                if (i>1) {
                    y = y + (i*diffHeight);
                }
                if (i == loopCounter)
                    y = y + diffHeight;
                y = y + wireDiff;

                context.fillStyle = color_for_stroke;
                context.fillRect(x, y, w, h);

                if (multiColorFlag)
                    context.fillStyle = multiColorSet[iterationForMultiBottom];
                else
                    context.fillStyle = fanoutColor;

                context.fillRect(x, y+wireStroke, w , h-(2*wireStroke));

                if (multiColorFlag)
                    drawPipe(CanvasTool, x, y, 'topLeft', multiColorSet[iterationForMultiBottom]);
                else
                    drawPipe(CanvasTool, x, y, 'topLeft', fanoutColor);

                // Drawing numbers only if fanout-6
                    // Bottom Right numbers
                CanvasTool.drawTextWithBorder(fanoutNumberLabel, 'black', 'white', 'black', "center", {x: x-w+60, y: y+5, w: w});

                iterationForMultiTop ++;
                iterationForMultiBottom--;
            }


            context.fillStyle = wireColor;

            context.lineWidth = 3;
            context.strokeStyle = color_for_stroke;

            var extraHeight = 0;
            if (parseInt(wiresCount) == 4)
                extraHeight = 5;

            var jacketMargin = 4*wireStroke;
            context.strokeRect( jacketCoords.x + (2*jacketMargin) , jacketCoords.y - (2*wireStroke), jacketCoords.w - (4*jacketMargin), jacketCoords.h + (4*wireStroke) + extraHeight);
            context.fillRect(jacketCoords.x + (2*jacketMargin) , jacketCoords.y - (2*wireStroke), jacketCoords.w - (4*jacketMargin), jacketCoords.h + (4*wireStroke) + extraHeight);

            context.fillStyle = '#2f2d2d';//color_for_stroke;
            context.fillRect(jacketCoords.x, jacketCoords.y - (6*wireStroke), 4*jacketMargin, jacketCoords.h + (12*wireStroke) + extraHeight);
            context.fillRect(jacketCoords.x + jacketCoords.w - (3*jacketMargin) , jacketCoords.y - (6*wireStroke), 4*jacketMargin, jacketCoords.h + (12*wireStroke) + extraHeight);

        }
        context.restore();

    }

    function redrawDuplexWire(CanvasToolObj, dir) {
    	if (dir === undefined)
    		dir='left';
        if (dir == 'right'){
            if (! CanvasToolObj.exceptions.uniboot.right)
                return;
        }
        else {
            if (! CanvasToolObj.exceptions.uniboot.left)
                return;
        }

        var bg_box_width = 27;
        var wireDiff = 11;

        var x = CanvasToolObj.canvasDim.centreX - CanvasToolObj.canvasDim.margin - (2*bg_box_width);
        var y = CanvasToolObj.canvasDim.centreY-CanvasToolObj.canvasWireDim.height;
        var w = CanvasToolObj.canvasDim.width/2 + (4*bg_box_width);

        var staticWireHeight = CanvasToolObj.canvasWireDim.height;
        var h = staticWireHeight;// This is the static height

        var color_for_wire = CanvasToolObj.defaultWireColor;
        var color_for_stroke = CanvasToolObj.defaultStrokeColor;
        var wireStroke = CanvasToolObj.wireStroke;

        w = w/3 + (bg_box_width);

        if (dir == 'right') {
            x = x + w + w/2 + bg_box_width;
        }

        // Redraw duplex wire by first clearing out the existing
        //  Top|Left|Bottom|Right
        // edges
        CanvasToolObj.context.clearRect(x, y-(2.5*bg_box_width), w, h+(5*bg_box_width));

        // Now draw the extended edge to modify the duplex wire
        // For edge #1
        CanvasToolObj.context.fillStyle = color_for_stroke;
        CanvasToolObj.context.fillRect(x-1, y, w+2, h);
        // For actual wire color
        CanvasToolObj.context.fillStyle = color_for_wire;
        CanvasToolObj.context.fillRect(x-1, y+wireStroke, w+2, h-(2*wireStroke));

        var dy = y + wireDiff;

        // For edge #2
        CanvasToolObj.context.fillStyle = color_for_stroke;
        CanvasToolObj.context.fillRect(x-1, dy, w+2, h);
        // For actual wire color
        CanvasToolObj.context.fillStyle = color_for_wire;
        CanvasToolObj.context.fillRect(x-1, dy+wireStroke, w+2, h-(2*wireStroke));

        return;
    }

    function redrawFanoutWire(CanvasToolObj, dir) {
    	if (dir === undefined)
    		dir='left';
        if (dir == 'right'){
            if (! CanvasToolObj.exceptions.mtp.right)
                return;
        }
        else {
            if (! CanvasToolObj.exceptions.mtp.left)
                return;
        }
        var hackForTopEdge = 21;
        var hackForTopRightEdge = 0;
        if (dir == 'right') {
            hackForTopEdge = 32;
            hackForTopRightEdge = 8;
        }

        var bg_box_width = 27;
        var wireDiff = 11;

        var x = CanvasToolObj.canvasWireDim.coordX1;
        var y = CanvasToolObj.canvasWireDim.coordY1;
        var w = CanvasToolObj.canvasWireDim.coordW;
        var h = CanvasToolObj.canvasWireDim.height;

        var diffHeight = 20;
        var diffWithinWire = 4;
        var diffWireBetween = 16;
        var wireMultiplier = 2;

        var color_for_wire = CanvasToolObj.defaultWireColor;
        var color_for_stroke = CanvasToolObj.defaultStrokeColor;
        var wireStroke = CanvasToolObj.wireStroke;

        // w = w/3 + (bg_box_width);

        if (dir == 'left') {
            x = x - 27;
        }
        if (dir == 'right') {
            x = x + (2*w) + w - (2*diffHeight);
            x = x + 3;
            x = x + (26);
        }


        // Redraw duplex wire by first clearing out the existing
        //  Top|Left|Bottom|Right
        // edges
        x = x - (w+w/2);
        w = w + (2*diffHeight) + 5;

        var dx = x;
        var dw = w;
        if (dir == 'left') {
            dx = dx - 27;
            dw = dw + 27;
        }
        if (dir == 'right'){
            dx = dx;
            dw = dw + 27;
        }
        CanvasToolObj.context.clearRect(dx, y-(6*diffHeight)-(diffWireBetween), dw, h+(12*diffHeight)+(3*diffWireBetween));

        // Draw wire only incase of 4-FiberCount
        // For edge|border
        // * Draw edge only if the fanout ount is above 24, i.e. 48, 72 etc
        if (CanvasToolObj.wireType.wiresCount > 24) {
            if (dir == 'left') {

                var dx = x;
                var hackMarginForPipe = 7;
                // Drawn as a set of 2(top & bottom), for hacking the issue with pipes visible beneath
                // CanvasToolObj.context.fillStyle = color_for_stroke;
                CanvasToolObj.context.fillRect(dx+w-16, y-(3.5*bg_box_width), 18, (8*bg_box_width)/2 - (hackMarginForPipe));
                // // For actual wire color
                CanvasToolObj.context.fillStyle = color_for_wire;
                CanvasToolObj.context.fillRect(dx+w-16+(1.5*wireStroke), y-(3.5*bg_box_width), 18-(3*wireStroke), (8*bg_box_width)/2 - hackMarginForPipe);

                CanvasToolObj.context.fillStyle = color_for_stroke;
                CanvasToolObj.context.fillRect(dx+w-16, y-(3.5*bg_box_width) + (8*bg_box_width)/2 + hackMarginForPipe, 18, (8*bg_box_width)/2 - hackMarginForPipe);
                // // For actual wire color
                CanvasToolObj.context.fillStyle = color_for_wire;
                CanvasToolObj.context.fillRect(dx+w-16+(1.5*wireStroke), y-(3.5*bg_box_width) + (8*bg_box_width)/2 + hackMarginForPipe, 18-(3*wireStroke), (8*bg_box_width)/2 - hackMarginForPipe);

            }
            else
            {
                var dx = x;
                var hackMarginForPipe = 7;
                // Drawn as a set of 2(top & bottom), for hacking the issue with pipes visible beneath
                CanvasToolObj.context.fillStyle = color_for_stroke;
                CanvasToolObj.context.fillRect(dx, y-(3.5*bg_box_width), 18, (8*bg_box_width)/2 - hackMarginForPipe);
                // // For actual wire color
                CanvasToolObj.context.fillStyle = color_for_wire;
                CanvasToolObj.context.fillRect(dx+(1.5*wireStroke), y-(3.5*bg_box_width), 18-(3*wireStroke), (8*bg_box_width)/2 - hackMarginForPipe);

                CanvasToolObj.context.fillStyle = color_for_stroke;
                CanvasToolObj.context.fillRect(dx, y-(3.5*bg_box_width) + (8*bg_box_width)/2 + hackMarginForPipe, 18, (8*bg_box_width)/2 - hackMarginForPipe);
                // // For actual wire color
                CanvasToolObj.context.fillStyle = color_for_wire;
                CanvasToolObj.context.fillRect(dx+(1.5*wireStroke), y-(3.5*bg_box_width) + (8*bg_box_width)/2 + hackMarginForPipe, 18-(3*wireStroke), (8*bg_box_width)/2 - hackMarginForPipe);
            }
        }
        y = y+h/2;
        h = 2*h;


        if (CanvasToolObj.wireType.wiresCount < 24) {

            /**
             * For Single Wire
             */
            // Incase of single wire
            // Now draw the extended edge to modify the duplex wire
            // For edge #1
            CanvasToolObj.context.fillStyle = color_for_stroke;
            CanvasToolObj.context.fillRect(x-1, y, w+2, h);
            // // For actual wire color
            CanvasToolObj.context.fillStyle = color_for_wire;
            CanvasToolObj.context.fillRect(x-1, y+wireStroke, w+2, h-(2*wireStroke));

        }
        else {


            // Drawing top pipe #1(inner pipe)
            if (dir == 'left') {
                var dx = x;
                drawPipe(CanvasToolObj, dx+w, y-5, 'topLeft', color_for_wire, {outerWidth: 18, innerWidth: 14, radius: 8});
                drawPipe(CanvasToolObj, dx+w-32, y-32, 'bottomRight', color_for_wire, {outerWidth: 18, innerWidth: 14, radius: 8});

                // Top Bottom Extra pipes(For 4-Fanouts)
                // * Draw only in case of wires-count for fanout above 24
                if (CanvasToolObj.wireType.wiresCount > 24) {
                    drawPipe(CanvasToolObj, dx+w-16-7, y-32- 3*bg_box_width - 5, 'bottomRight', color_for_wire, {outerWidth: 18, innerWidth: 14, radius: 8});
                    drawPipe(CanvasToolObj, dx+w-16-7, y-32- 3*bg_box_width - 5 + 9*bg_box_width, 'topRight', color_for_wire, {outerWidth: 18, innerWidth: 14, radius: 8});
                }
            }
            else
            {
                var dx = x;
                drawPipe(CanvasToolObj, dx, y-5, 'topRight', color_for_wire, {outerWidth: 18, innerWidth: 14, radius: 8});
                drawPipe(CanvasToolObj, dx+32, y-32, 'bottomLeft', color_for_wire, {outerWidth: 18, innerWidth: 14, radius: 8});

                // Top Bottom Extra pipes(For 4-Fanouts)
                // * Draw only in case of wires-count for fanout above 24
                if (CanvasToolObj.wireType.wiresCount > 24) {
                    drawPipe(CanvasToolObj, dx+32-7, y-32- 3*bg_box_width - 5, 'bottomLeft', color_for_wire, {outerWidth: 18, innerWidth: 14, radius: 8});
                    drawPipe(CanvasToolObj, dx+32-7, y-32- 3*bg_box_width - 5 + 9*bg_box_width, 'topLeft', color_for_wire, {outerWidth: 18, innerWidth: 14, radius: 8});
                }
            }


            if (dir == 'right') {
                x = x + 31.5;
            }

            // Now draw the extended edge to modify the duplex wire
            // For edge #1
            CanvasToolObj.context.fillStyle = color_for_stroke;
            CanvasToolObj.context.fillRect(x, y-(2*wireStroke)-34, w-32, h-wireStroke);
            // // For actual wire color
            CanvasToolObj.context.fillStyle = color_for_wire;
            CanvasToolObj.context.fillRect(x, y-(wireStroke/2)-34, w-31, h-(4*wireStroke));

            // For inner top wire
            var text = '';
            if (CanvasToolObj.wireType.wiresCount == 24)
                text = '1';
            else
                text = '2';

            var dx = x+w/2-45;
            if (dir == 'right')
                dx = x-62-25;
            if (dir == 'left')
                dx = dx + 12;

            CanvasToolObj.drawTextWithBorder(text, 'black', 'white', 'black', "center", {x: dx, y: y-(2*wireStroke)-34 + 6, w: w});


            // Draw the extended edge to for top(Only in case of Fnaouts-4)
            // * Draw only in case of wires-count for fanout above 24
            if (CanvasToolObj.wireType.wiresCount > 24) {

                var dx = x;
                CanvasToolObj.context.fillStyle = color_for_stroke;
                CanvasToolObj.context.fillRect(dx-(hackForTopRightEdge), y-32- 3*bg_box_width - 10, w-22, h-(1.75*wireStroke));
                // // For actual wire color
                CanvasToolObj.context.fillStyle = color_for_wire;
                CanvasToolObj.context.fillRect(dx-hackForTopRightEdge, y-32- 3*bg_box_width - 9.5 + wireStroke, w-19, h-(4.75*wireStroke));

                dx = x+w/2-45;
                if (dir == 'right')
                    dx = x-62-25;
                if (dir == 'left')
                    dx = dx + 12;

                CanvasToolObj.drawTextWithBorder('1', 'black', 'white', 'black', "center", {x: dx, y: y-32- 3*bg_box_width - 10 + 6, w: w});
            }

            // Drawing bottom pipe #1(inner pipe)
            y = y+9;
            if (dir == 'left') {
                var dx = x;
                drawPipe(CanvasToolObj, dx+w, y+5, 'bottomLeft', color_for_wire, {outerWidth: 18, innerWidth: 13, radius: 8});
                drawPipe(CanvasToolObj, dx+w-32, y+32, 'topRight', color_for_wire, {outerWidth: 18, innerWidth: 13, radius: 8});
            }
            else
            {
                var dx = x;
                drawPipe(CanvasToolObj, dx-32, y+5, 'bottomRight', color_for_wire, {outerWidth: 18, innerWidth: 13, radius: 8});
                drawPipe(CanvasToolObj, dx, y+32, 'topLeft', color_for_wire, {outerWidth: 18, innerWidth: 13, radius: 8});
            }

            y = y+72-9+1;
            var dx = x;
            // Now draw the extended edge to modify the duplex wire
            // For edge #1
            CanvasToolObj.context.fillStyle = color_for_stroke;
            CanvasToolObj.context.fillRect(dx, y-(2*wireStroke)-34, w-32, h-wireStroke);
            // // For actual wire color
            CanvasToolObj.context.fillStyle = color_for_wire;
            CanvasToolObj.context.fillRect(dx, y-(wireStroke/2)-34, w-31, h-(4*wireStroke));

            var text = '';
            var outputText = CanvasToolObj.wireType.wiresCount/12;
            if (outputText <= 2)
                text = '2';
            else
                text = '..';

            dx = x+w/2-45;
            if (dir == 'right')
                dx = x-62-25;
            if (dir == 'left')
                dx = dx + 12;

            CanvasToolObj.drawTextWithBorder(text, 'black', 'white', 'black', "center", {x: dx, y: y-(2*wireStroke)-34 +6, w: w});

            // Now draw the extended edge to modify the duplex wire
            // For edge #1

            // * Draw only in case of wires-count for fanout above 24
            if (CanvasToolObj.wireType.wiresCount > 24) {
                var dx = x;
                CanvasToolObj.context.fillStyle = color_for_stroke;
                CanvasToolObj.context.fillRect(dx-(hackForTopRightEdge), y-(2*wireStroke)-31 + (3*bg_box_width), w-hackForTopEdge + (hackForTopRightEdge-1), h-wireStroke);
                // // For actual wire color
                CanvasToolObj.context.fillStyle = color_for_wire;
                CanvasToolObj.context.fillRect(dx-(hackForTopRightEdge), y-(wireStroke/2)-31 + (3*bg_box_width), w-(hackForTopEdge-1) + hackForTopRightEdge, h-(4*wireStroke));

                var text = CanvasToolObj.wireType.wiresCount/12;
                dx = x+w/2-45;
                if (dir == 'right')
                    dx = x-62-25;
                if (dir == 'left')
                    dx = dx + 12;

                CanvasToolObj.drawTextWithBorder(text, 'black', 'white', 'black', "center", {x: dx, y: y-(2*wireStroke)-31 + (3*bg_box_width) + 6, w: w});
            }
        }

        return;
    }

    function drawText(CanvasToolObj, stringObj, coords) {

        // var maxWidth = 400;
        var lineHeight = CanvasToolObj.textConfig.lineHeight;
        lineHeight = 20;
        CanvasToolObj.context.font = CanvasToolObj.DEFAULT_FONT_SIZE+ ' '+CanvasToolObj.DEFAULT_FONT_FAMILY;;
        CanvasToolObj.context.fillStyle = '#000';

        // if (stringObj.type.indexOf('left') != -1)
        //     CanvasToolObj.context.textAlign = "left";

        // if (stringObj.type.indexOf('right') != -1)
            CanvasToolObj.context.textAlign = "center";

        if (stringObj.type == 'top' || stringObj.type == 'bottom')
            CanvasToolObj.context.textAlign = "center";

        // CanvasToolObj.context.fillText(stringObj.text, coords.x, coords.y);
        wrapText(CanvasToolObj.context, stringObj.text.toUpperCase(), coords.x, coords.y, coords.w, lineHeight);


    }

    function wrapText(context, text, x, y, maxWidth, lineHeight) {
        var words = text.split(' ');
        var line = '';
        for(var n = 0; n < words.length; n++) {
            var testLine = line + words[n] + ' ';
            var metrics = context.measureText(testLine);
            var testWidth = metrics.width;
            if (testWidth > maxWidth && n > 0) {
                context.fillText(line, x, y);
                line = words[n] + ' ';
                y += lineHeight;
            }
            else {
                line = testLine;
            }
        }
        context.textAlign = 'center';
        context.fillText(line, x, y);

    }

    //DrawImage wrapper for canvas drawImage
    function canvasDrawImage(GlobalCanvasObj, x, y, image_url, type, globalDrawnLogObj, captureCanvasOutputObj) {
    	if (type === undefined)
    		type=0;
    	if (captureCanvasOutputObj.status === undefined)
    		captureCanvasOutputObj.status = false;
    	if (captureCanvasOutputObj.key === undefined)
    		captureCanvasOutputObj.key = '';
        var showLoaderUniqueKey = 'canvasDrawImage'+getRandomArbitrary(0, 999999999).toString();
        var showLoaderUniqueKey2 = 'canvasDrawImage2'+getRandomArbitrary(0, 999999999).toString();
        GlobalCanvasObj.showLoader(true, showLoaderUniqueKey);
        GlobalCanvasObj.showLoader(true, showLoaderUniqueKey2);
        var canvasimageObj = new Image();
        var context = GlobalCanvasObj.context;

        // Check if scaling is required when drawing, incae of fanouts-6, rescale the elements
        var scale = false;

        if (GlobalCanvasObj.wireType.type.toLowerCase() == 'fanout' && GlobalCanvasObj.wireType.wiresCount >= 6)
            scale = true;

        // Irrelevant in-case of scale=true, as uniboots are drawn in case of duplex wires only
        var lScale = rScale = false;
        if (GlobalCanvasObj.exceptions.uniboot.left && type == 'leftBoot'){
            lScale = true;
            scale = false;
        }
        if (GlobalCanvasObj.exceptions.uniboot.right && type == 'rightBoot') {
            rScale = true;
            scale = false;
        }

        canvasimageObj.onload = function () {

            if (type == 'rightBoot') {

                if (GlobalCanvasObj.exceptions.mtp.right == true) {
                    rScale = true;
                    scale = false;
                }

                // Flipping image
                context.save();
                context.scale(-1, 1);// Flipping/mirroring effect

                if (scale == true) {
                    context.drawImage(canvasimageObj, x*-1, y + (canvasimageObj.height*0.20)/2 , (canvasimageObj.width*0.80), (canvasimageObj.height*0.80));
                }
                else if (rScale == true) {
                    context.drawImage(canvasimageObj, x*-1, y - (canvasimageObj.height*0.12) - 1 , canvasimageObj.width + (canvasimageObj.width*0.12), canvasimageObj.height+(2*canvasimageObj.height*0.12));
                }
                else {
                    context.drawImage(canvasimageObj, x*-1, y);
                }


                context.restore();
            }

            if (type == 'leftBoot') {
                if (GlobalCanvasObj.exceptions.mtp.left){
                    lScale = true;
                    scale = false;
                }
                context.save();

                if (scale == true)
                    context.drawImage(canvasimageObj, x, y + (canvasimageObj.height*0.20)/2 , (canvasimageObj.width*0.80), (canvasimageObj.height*0.80));
                else if (lScale == true) {
                    context.drawImage(canvasimageObj, x, y - (canvasimageObj.height*0.12) - 1 , canvasimageObj.width + (canvasimageObj.width*0.12), canvasimageObj.height+(2*canvasimageObj.height*0.12));
                }
                else {
                    context.drawImage(canvasimageObj, x, y);
                }

                context.restore();
            }

            // y = y-10
            if (type == 'leftConnector') {
                x = x - canvasimageObj.width;
                context.save();

                var coordsForLog = {x: x, y: y, w: canvasimageObj.width, h: canvasimageObj.height};
                if (scale == true) {
                    context.drawImage(canvasimageObj, x + (canvasimageObj.width * 0.20), y + (canvasimageObj.height*0.20)/2 , (canvasimageObj.width*0.80), (canvasimageObj.height*0.80));
                    coordsForLog = {x: x + (canvasimageObj.width * 0.20), y: y + (canvasimageObj.height*0.20)/2, w: (canvasimageObj.width*0.80), h: (canvasimageObj.height*0.80)}
                }
                else
                    context.drawImage(canvasimageObj, x, y);

                context.restore();

                GlobalCanvasObj.updateDrawnConnectors('left', coordsForLog);
            }


            if (type == 'repaintCanvas') {
                x = 0;
                y = 0;
                context.drawImage(canvasimageObj, x, y);
            }

            if (type == 'rightConnector') {
                x = x + canvasimageObj.width
                // Flipping image
                context.save();
                context.scale(-1, 1);// Flipping/mirroring effect

                var coordsForLog = {x: x, y: y, w: canvasimageObj.width, h: canvasimageObj.height};
                if (scale == true) {
                    context.drawImage(canvasimageObj, x*-1 + (canvasimageObj.width*0.25), y + (canvasimageObj.height*0.25)/2 , (canvasimageObj.width*0.75), (canvasimageObj.height*0.75));
                    coordsForLog = {x: x*-1 + (canvasimageObj.width*0.20), y: y + (canvasimageObj.height*0.20)/2, w: (canvasimageObj.width*0.80), h: (canvasimageObj.height*0.80)};
                }
                else
                    context.drawImage(canvasimageObj, x*-1, y);

                context.restore();

                GlobalCanvasObj.updateDrawnConnectors('right', coordsForLog);
            }

            if (type == 'bothBoots') {
                // Draw left image as is
                context.save();
                context.drawImage(canvasimageObj, x, y);

                // Flipping image
                context.translate(x, y);
                context.scale(-1, -1);// Flipping/mirroring effect
                context.drawImage(canvasimageObj, x, 0);
                context.restore();
            }

            if (type == 'bootFlip') {
                // Flipping image
                context.save();
                context.translate(x, y);
                context.scale(-1, -1);// Flipping/mirroring effect
                context.drawImage(canvasimageObj, x, y);
                context.restore();
            }

            if (type == 'cassette') {
                var dh = canvasimageObj.height;// * 0.70;
                var dw = canvasimageObj.width;// * 0.70;
                context.drawImage(canvasimageObj, x-(dw)/2, y - (dh)/2 - 25, dw, dh );
            }

            if (type == 'cassetteConnector') {
                var dh = canvasimageObj.height;// * 0.70;
                var dw = canvasimageObj.width;// * 0.70;
                context.drawImage(canvasimageObj, x-(2*dw)-71, y - (dh)/2 - 25, dw, dh );
            }

            if (type == 'leftNodeHousing') {
                var dh = canvasimageObj.height;
                var dw = canvasimageObj.width;
                // console.log("Drawing node housing", x, y, dw, dh);
                context.drawImage(canvasimageObj, x, y - (dh)/2, dw, dh);
            }

            if (type == 'rightNodeHousing') {
                var dh = canvasimageObj.height;
                var dw = canvasimageObj.width;

                x = x + canvasimageObj.width
                // Flipping/mirroring effect
                context.save();
                context.scale(-1, 1);

                context.drawImage(canvasimageObj, x*-1, y - (dh)/2, dw, dh);
                context.restore();

            }

            // Log the figure details
            logDrawn(globalDrawnLogObj, {x1: x, y1: y, w: canvasimageObj.width, h: canvasimageObj.height}, type);
            if (captureCanvasOutputObj.status == true){
                GlobalCanvasObj.captureCanvasOutput(captureCanvasOutputObj.key);
            }

            GlobalCanvasObj.showLoader(false, showLoaderUniqueKey);
        };
        canvasimageObj.src = image_url;
        // sleep(10);
        GlobalCanvasObj.showLoader(false, showLoaderUniqueKey2);
        return {x: x, y: y, w: canvasimageObj.width, h: canvasimageObj.height};
    }

    //DrawPipe for double wires
    function drawPipe(CanvasTool, startX, startY, dir, wireColor, pipeDim) {

    	if (wireColor === undefined)
    		wireColor=CanvasTool.defaultWireColor;
    	if (pipeDim === undefined)
    		pipeDim = {outerWidth: undefined, innerWidth: undefined, radius: undefined};
    	if (pipeDim.outerWidth === undefined)
    		pipeDim.outerWidth = 10;
    	if (pipeDim.innerWidth === undefined)
    		pipeDim.innerWidth = 6.5;
    	if (pipeDim.radius === undefined)
    		pipeDim.radius = 10;
        // Tetsing ars implementation
        var startAngle;
        var endAngle;

        var radius = pipeDim.radius;

        switch(dir){
            case 'topRight':
                var startY = startY + radius - radius/2,
                    startX = startX,
                    startX1 = startX,
                    startY1 = startY,
                    startX2 = startX+(2*radius),
                    startY2 = startY1-(2*radius),
                    startdX1 = startX-0.5,
                    // startdX1 = startX,
                    startdY1 = startY1,
                    startdX2 = startX+(2*radius),
                    startdY2 = startdY1-(2*radius)-0.5;
                break;

            case 'topLeft':
                var startY = startY + radius - radius/2,
                    startX  = startX,
                    startX1 = startX,
                    startY1 = startY,
                    startX2 = startX-(2*radius),
                    startY2 = startY1-(2*radius),
                    startdX1 = startX+0.5,
                    startdY1 = startY1,
                    startdX2 = startX-(2*radius),
                    startdY2 = startdY1-(2*radius)-0.5;
                break;

            case 'bottomLeft':
                var startY = startY + radius - radius/2,
                    startX1 = startX,
                    startY1 = startY,
                    startX2 = startX-(2*radius),
                    startY2 = startY1+(2*radius),
                    startdX1 = startX+0.5,
                    startdY1 = startY1,
                    startdX2 = startX-(2*radius),
                    startdY2 = startdY1+(2*radius)+0.5;
                break;

            case 'bottomRight':
                var startY = startY + radius - radius/2,
                    startX1 = startX,
                    startY1 = startY,
                    startX2 = startX+(2*radius),
                    startY2 = startY1+(2*radius),
                    startdX1 = startX-0.5,
                    startdY1 = startY1,
                    startdX2 = startX+(2*radius),
                    startdY2 = startdY1+(2*radius)+0.5;
                break;
        }

        CanvasTool.context.beginPath();

        // For outer edge
        CanvasTool.context.strokeStyle = CanvasTool.defaultStrokeColor;
        CanvasTool.context.lineWidth = pipeDim.outerWidth;
        CanvasTool.context.moveTo(startX1, startY1);
        CanvasTool.context.quadraticCurveTo(startX2, startY1, startX2, startY2);
        CanvasTool.context.stroke();

        // For inner edge
        CanvasTool.context.beginPath();

        CanvasTool.context.strokeStyle = wireColor;
        CanvasTool.context.lineWidth = pipeDim.innerWidth;
        CanvasTool.context.moveTo(startdX1, startdY1);
        CanvasTool.context.quadraticCurveTo(startdX2, startdY1, startdX2, startdY2);
        CanvasTool.context.stroke();

    }

    //DrawPipe for double wires
    function drawPipeOriginal(CanvasTool, startX, startY, dir) {
        // Tetsing ars implementation
        var startAngle;
        var endAngle;
        switch(dir){
            case 'topRight':
                startAngle = 0*Math.PI;
                endAngle = 0.5*Math.PI;
                startY = startY-10-5.5;
                break;

            case 'topLeft':
                startAngle = 0.5*Math.PI;
                endAngle = 1*Math.PI;
                startY = startY-10-5.5;
                break;

            case 'bottomLeft':
                startAngle = 1.5*Math.PI;
                endAngle = 2*Math.PI;
                startY = startY+20+5.5;
                break;

            case 'bottomRight':
                startAngle = 1*Math.PI;
                endAngle = 1.5*Math.PI;
                startY = startY+20+5.5;
                break;
        }

        // Making initial layer for border-effect
        CanvasTool.context.beginPath();
        CanvasTool.context.strokeStyle = CanvasTool.defaultStrokeColor;
        CanvasTool.context.arc(startX, startY, 20, startAngle, endAngle);
        CanvasTool.context.lineWidth = 10;
        CanvasTool.context.stroke();

        // Filling the arc with the actual color
        CanvasTool.context.beginPath();
        CanvasTool.context.strokeStyle = CanvasTool.defaultWireColor;
        CanvasTool.context.arc(startX, startY, 20, startAngle, endAngle);
        CanvasTool.context.lineWidth = 7;
        CanvasTool.context.stroke();

    }

    //Draw Figures Log
    function drawFiguresLog(CanvasTool, funcName, params) {
        var funcParams = params;

        CanvasTool.drawnFiguresLog.push(funcName + "(" + funcParams + ")");

    }

    //Log Drawn/Last Drawn Figure, includes wires/boots/connectors
    function logDrawn(globalDrawnObj, objToLog, objType) {
        dataToLog = {
            type: objType,
            dim:  objToLog,
        }
        globalDrawnObj.stack.push(dataToLog);
        globalDrawnObj.lastDrawn = dataToLog;

    }

    // Log text drawn
    /**
     * @param data: { text: string, type: 'left'|'top'|'right'|'bottom', coords: {}  }
     */
    function logDrawnText(GlobalCanvasObj, data) {
        for (var key in GlobalCanvasObj.drawnText) {
            if (GlobalCanvasObj.drawnText.hasOwnProperty(type)) {
                GlobalCanvasObj.drawnText[key] = data;
            }
        }

    }

    /**
     * Method to keep track of the canvas output for undo-redo
     * Saves the current canvas output as jpeg
     */
    function captureCanvasOutput(GlobalCanvasObj, key) {

        //Updating the current history index
        var showLoaderUniqueKey = 'captureCanvasOutput'+getRandomArbitrary(0, 999999999).toString();
        var showLoaderUniqueKey2 = 'captureCanvasOutput'+getRandomArbitrary(0, 999999999).toString();
        GlobalCanvasObj.showLoader(true, showLoaderUniqueKey);
        GlobalCanvasObj.showLoader(true, showLoaderUniqueKey2);

        var imgToDrawIndex = -1;
        for (var i=0; i<GlobalCanvasObj.history.length; i++) {
            if (GlobalCanvasObj.history[i].key == key){
                newEntry = false;
                imgToDrawIndex = i;
                break;
            }
        }
        setTimeout(function() {

            var newEntry=true;
            GlobalCanvasObj.history[imgToDrawIndex] = {key: key, image: GlobalCanvasObj.canvas.toDataURL('image/png', 0.85)};

            if (newEntry == true) {
                GlobalCanvasObj.currentHistoryIndex++;
                GlobalCanvasObj.history.push({key: key, image: GlobalCanvasObj.canvas.toDataURL('image/png', 0.85)});
            }

            GlobalCanvasObj.showLoader(false, showLoaderUniqueKey);
        }, 1200);

        GlobalCanvasObj.showLoader(false, showLoaderUniqueKey2);

        return;
    }

    function recoverCanvasOutput(GlobalCanvasObj, image) {

        var showLoaderUniqueKey = 'recoverCanvasOutput'+getRandomArbitrary(0, 999999999).toString();
        var showLoaderUniqueKey2 = 'recoverCanvasOutput'+getRandomArbitrary(0, 999999999).toString();
        GlobalCanvasObj.showLoader(true, showLoaderUniqueKey);
        GlobalCanvasObj.showLoader(true, showLoaderUniqueKey2);
        var canvasimageObj = new Image();

        canvasimageObj.onload = function () {
            var context = GlobalCanvasObj.context;
            x = 0;
            y = 0;

            context.drawImage(canvasimageObj, x, y);
            GlobalCanvasObj.showLoader(false, showLoaderUniqueKey);
        };
        canvasimageObj.src = image;
        GlobalCanvasObj.showLoader(false, showLoaderUniqueKey2);
    }

    function duplexWire(ctx, x, y, width, height, radius, fill, stroke) {
      fill = true;
      if (typeof stroke == 'undefined') {
        stroke = true;
      }
      if (typeof radius === 'undefined') {
        radius = 5;
      }
      if (typeof radius === 'number') {
        radius = {tl: radius, tr: radius, br: radius, bl: radius};
      } else {
        var defaultRadius = {tl: 0, tr: 0, br: 0, bl: 0};
        for (var side in defaultRadius) {
          radius[side] = radius[side] || defaultRadius[side];
        }
      }
      ctx.beginPath();
      ctx.moveTo(x + radius.tl, y);
      ctx.lineTo(x + width - radius.tr, y);
      ctx.quadraticCurveTo(x + width, y, x + width, y + radius.tr);
      ctx.lineTo(x + width, y + height - radius.br);
      ctx.quadraticCurveTo(x + width, y + height, x + width - radius.br, y + height);
      ctx.lineTo(x + radius.bl, y + height);
      ctx.quadraticCurveTo(x, y + height, x, y + height - radius.bl);
      ctx.lineTo(x, y + radius.tl);
      ctx.quadraticCurveTo(x, y, x + radius.tl, y);
      ctx.closePath();
      if (fill) {
        ctx.fill();
      }
      if (stroke) {
        ctx.stroke();
      }
    }

}());


/**
 * -Canvas GUI App
 * -Integrating the Canvas-Tool above with the GUI
 * -And Canvas Menu
 */
( function(){

    this.CanvasGUI = function() {

        /**
         * Components list
         */
        // Must be a valid jQuery selector, and immediate parent
        // of the actual Canvas-Menu links
        this.menuContainer = "ol#steps-customs";

        // Must be a valid jQuery selector, and represent a canvas
        // element used for the CanvasGraphics generation
        this.CanvasContainer = "#canvas_config2";

        this.$firstMenu = $(this.menuContainer).find('li').eq(0);

        this.specialClasses = [
            {submenuActive: 'sub-menu-active'}
        ];

        /**
         * CanvasObj Wrapper implementation
         */
        this.canvasToolObj = undefined;

        this.hideSimplexOption = function() {
            $(this.menuContainer).find('li[data-config-name="fiber_count"]').find('ul [data-cgui-component-name="SIMPLEX"]').parent().addClass('hidden');
        }

        this.hideUniBootConnectors = function() {
            $(this.menuContainer).find('li[data-config-name="fiber_count"]').find('ul [data-cgui-component-name="SIMPLEX"]').parent().addClass('hidden');
        }

        this.showUniBootConnectors = function() {
            $(this.menuContainer).find('li[data-config-name="fiber_count"]').find('ul [data-cgui-component-name="SIMPLEX"]').parent().addClass('hidden');
        }

        this.showSimplexOption = function() {
            $(this.menuContainer).find('li[data-config-name="fiber_count"]').find('ul [data-cgui-component-name="SIMPLEX"]').parent().removeClass('hidden');
        }

        this.hideDuplexOption = function() {
            $(this.menuContainer).find('li[data-config-name="fiber_count"]').find('ul [data-cgui-component-name="DUPLEX"]').parent().addClass('hidden');
        }

        this.showDuplexOption = function() {
            $(this.menuContainer).find('li[data-config-name="fiber_count"]').find('ul [data-cgui-component-name="DUPLEX"]').parent().removeClass('hidden');
        }

        this.disableRightClickOnCanvas = function() {
            return jQuery('body').on('contextmenu', this.CanvasContainer, function(e){ return false; });
        }

        /**
         * //End of CanvasObj Wrappers
         */


        /**
         * jQuery object
         */
        this.currentActiveMenu = '';

        this.models = {
            menus: [],
            conditions: {
                db: [],
                staticConds: [],
                price: {
                    db: [],
                    staticConds: ''
                }

            },
        };

        this.resetCanvas = function() {
            this.CanvasToolObj.clearCanvas();
            this.CanvasToolObj.resetPrice(this);
            this.CanvasToolObj.resetWeight(this);
            this.CanvasToolObj.resetPartNumber(this);

            $(this.menuContainer)
                .find('li>ul.sub-content>li')
                .removeClass('active-subitem');
            $(this.menuContainer)
                .find('li.parent-element')
                .removeClass('clicked-item');

            updateCurrentActiveMenu(this, false);

            this.hideAll();

            this.$firstMenu
                .addClass('active');

            this.$firstMenu
                .find('ul.sub-content')
                .removeClass('hidden');

            this.$firstMenu.find('ul.sub-content').removeClass('hidden');
        }

        this.resetMenu = function() {
            // Show the default first element and reset all other
            // sub links
            this.hideAll();

            // Populate the first li, and display the default content
            // This content is the content that was preserved on intial page load
            // update the current active menu

            updateCurrentActiveMenu(this);

            // this.populateParentElement(this.models.menus[0]);
            this.$firstMenu
                .find('ul.sub-content')
                .removeClass('hidden');

            this.$firstMenu.find('ul.sub-content').removeClass('hidden');//.addClass('active');

            return true;
        }

        /**
         * Enable printing on canvas, only in-case its the last step
         * return
         *  string|base64 encoded, image output of the canvas
         */
        this.printCanvasOutput = function() {

            var thisObj = this;

            var continuePrint = false;
            for (var i=thisObj.selectedOptions.length-1; i>=0; i--) {
                if ( jQuery('ol#steps-customs').find('li.parent-element').last().data('config-name').toLowerCase() == thisObj.selectedOptions[i].key.toLowerCase() )
                    continuePrint = true;
            }
            if (!continuePrint)
                return false;
            thisObj.CanvasToolObj.showLoader(true, 'print');

            // insert logo and/or other details before printing
            var canvas = document.getElementById('canvas_config');
            var context = canvas.getContext('2d');
            thisObj.CanvasToolObj.addLogoToCanvas(canvas, context);

            // // Drawing image from main-canvas
            var origImageObj = new Image();
            origImageObj.onload = function() {
                context.drawImage(origImageObj, 0, 0);

                // Draw text on canvas
                context.font = '12px serif';
                var additionalTextForPrint = 'Part Number:'+thisObj.CanvasToolObj.partNumber;
                context.fillText(additionalTextForPrint, 10, 20);

                if (thisObj.CanvasToolObj.price > 0){
                    additionalTextForPrint = 'Price: $'+thisObj.CanvasToolObj.price;
                    context.fillText(additionalTextForPrint, 10, 35);
                }

                img = canvas.toDataURL('image/png', 0.85);

                // var w = window.open();
                // w.document.write('<img src="'+img+'">');
                // w.document.close();
                // w.focus();
                // w.print();
                // var printElem = document.createElement("div");
                // printElem.innerHtml = "<img src='"+ img +"'>";
                // window.print(printElem);

                thisObj.CanvasToolObj.addLogoToCanvas(canvas, context);

                printJS(
                    {
                        printable: img,
                        type: 'image',
                        documentTitle: 'Megladon Custom Cables',
                        showModal: true,
                    }
                );

                thisObj.CanvasToolObj.showLoader(false, 'print');
            }
            origImageObj.src = thisObj.CanvasToolObj.canvas.toDataURL('image/png', 0.85);

            // return this.CanvasToolObj.canvas.toDataURL('image/png', 0.85);

        }

        function generateHtmlForSubMenu(jsonContent) {

            if(jsonContent.length == 0)
                return false;
                var html = "";

            for(var i=0; i<jsonContent.length; i++){
                html +=  "<li data-config-name='"+ jsonContent[i].configName +"'>";
                html += "<a href='#' ";

                // Add clickable for non-input field sub-menus
                if (jsonContent[i].cguiFieldType != '1')
                    html += " data-canvas-menu-trigger ";

                if (i>0)
                    html += " disabled ";
                html += "data-cGui-title='"+ jsonContent[i].cguiTitle +"' ";
                html += "data-cGui-img-uri='"+ jsonContent[i].cguiImgUri +"' ";
                html += "data-cGui-component-id='"+ jsonContent[i].cguiComponentId +"' ";
                html += "data-cGui-component-name='"+ (jsonContent[i].cguiComponentName) +"' ";
                html += "data-cGui-component-part-number='"+ jsonContent[i].cguiComponentPartNumber +"' ";
                html += "data-cGui-field-type='"+jsonContent[i].cguiFieldType+"'";
                html += "data-config-id='"+ jsonContent[i].configId +"' ";
                html += "data-config-name='"+jsonContent[i].configName+"'";
                html += "'data-canvas-image='"+ jsonContent[i].canvasImage +" '";
                html += "data-config-term-meta='"+ jsonContent[i].configTermMeta +"' ";
                html += "data-cGui-component-price='"+ jsonContent[i].cGuiComponentPrice +"' >";
                html += "<span class='graphics-img'><img class='pro-lbl' alt='' src='"+ jsonContent[i].cguiImgUri +"'></span> ";
                html += "<p>"+ jsonContent[i].cguiComponentName.toLowerCase() +"</p> ";

                if (typeof(jsonContent[i].cguiComponentDescription) == 'string') {
                    var tooltip_desc = jsonContent[i].cguiComponentDescription;
                    html += "<label  data-toggle='tooltip' data-placement='top' title='"+ tooltip_desc +"' class='tooltip-icn'><i class='fa fa-info' aria-hidden='true'></i></label>";
                }

                // For boots, color options
                html += "</a>";
                if (jsonContent[i].cguiFieldType == '1')
                    html += "<input type='text' placeholder='0'>";
                html += "</li>";

                // Preloading images

            }
            return html;
        }

        function runJsAdditionalScriptsHack() {
            // Hack, enabling the tooltip
            $('[data-toggle="tooltip"]').tooltip();
        }

        this.hideAll = function() {
            // Hide all active menus

            resetAllMenus(this.menuContainer);

            // $(this.menuContainer).find('li.parent-element>a').attr('disabled', 'disabled');
            $(this.menuContainer)
                .find('li>ul.sub-content')
                .addClass('hidden');

            $(this.menuContainer)
                .find('li div.left-box')
                .addClass('hidden');

            return true;
        }

        this.setStaticConditions = function(jsonObj) {

            var keySet = [];
            // For Cable
            var currKeySet = {
                for: '',
                type: '',
                dataset: [],

            };
            this.models.conditions.staticConds.push(jsonObj);
        }

        this.setDbConditions = function(jsonObj) {
            this.models.conditions.db.push(jsonObj);
        }

        this.setInitialData = function(jsonObj) {
            return setInitialData(this, jsonObj);
        }

        this.addListenerForSubMenus = function() {
            addListenerForSubMenus(this);
        }

        this.addListenerForMainMenus = function() {
            return addListenerForMainMenus(this);
        }

        this.selectedOptions = [];

        this.preloadImages = function() {
            for(var i=0; i<this.models.menus.length; i++){
                preloadImages(this.models.menus[i].canvasImage);
            }

            $('ol#steps-customs [data-canvas-image]').each(function(k, v) {

                    var uniqueImages = [];
                    var uniqueImagesArr = [];
                    var images = $(v).data('canvas-image');//.data('canvas-image');
                    if ( images !== null && images.length > 0) {
                        for (var i=0; i<images.length; i++) {

                            if ($.inArray(images[i], uniqueImages) !== -1)
                                    continue;

                            uniqueImages.push();
                            uniqueImagesArr.push(images[i]);
                        }
                    }

                    preloadImages(uniqueImagesArr);
            })



        }

        function preloadImages(sources, callback) {
            var images = {};
            var loadedImages = 0;
            var numImages = 0;
            for (var src in sources) {
                numImages++;
            }
            for (var src in sources) {
                images[src.img] = new Image();
                images[src.img].onload = function () {
                   // Do some fancy stuff!
                };
                images[src.img].src = sources[src.img];
            }
        }

        // Start off the action
        this.init = function() {
            // Init the models for all the possible initils data elements
            // Filteration will be performed later on these elements
            this.CanvasToolObj = new CanvasTool();
            this.resetMenu();
            this.addListenerForMainMenus();
            this.addListenerForSubMenus(this);


        }

        this.processDBConditions = function() {
            this.CanvasToolObj.activeConds.db = {};
            this.CanvasToolObj.updateActiveConds('db', processDBConditions(this));
            return;
        }

        this.processStaticConditions = function() {
            this.CanvasToolObj.activeConds.staticConds = {};
            this.CanvasToolObj.updateActiveConds('staticConds', processStaticConditions(this));
            return;
        }

        this.updateLogoImgUrl = function(imgUrl) {
            this.CanvasToolObj.updateLogoImgUrl(imgUrl);
            return;
        }

        function setInitialData(CGuiObj, jsonObj) {
            return CGuiObj.models.menus.push(jsonObj);
        }

        function resetAllMenus(menuContainer) {
            $(menuContainer)
                .find('li.parent-element')
                .removeClass('active visited visited-by-submenu temp-active')
                .find('li.parent-element>a')
                .removeAttr('disabled');
                // .attr('disabled', true)
        }

        function addListenerForMainMenus(CGuiObj) {
            $(CGuiObj.menuContainer)
                .find('li.parent-element>a')
                .on('click', function(e) {
                    e.preventDefault();
                    //Update the current menu attribute
                    // Find if any of the next element has active class
                    // Allow to open only if it does
                    var found = findIfGroupHasClass($(this).parents('li.parent-element').nextAll());
                    var foundAll = findIfGroupHasClass($('ol#steps-customs').find('li.parent-element'));
                    if ($(this).parents('li.parent-element').hasClass('active')  || (found == true) || (foundAll == false) ) {
                        toggleMenu(CGuiObj, $(this));
                    }

                })
        }

        function findIfGroupHasClass($group) {
            var found = false;
            $group.each(function(key, val) {
                if ($(val).hasClass('active')) {
                    found = true;
                }
            })

            return found;
        }

        function toggleMenu(CGuiObj, $this) {

            // Filter the contents of the next menu
            // Hide all first
            $('ol#steps-customs')
                .find('ul.sub-content')
                .addClass('hidden');

            $('ol#steps-customs')
                .find('li.parent-element:not(.visited)')
                .removeClass('active');

            $('ol#steps-customs')
                .find('li.parent-element.visited')
                .addClass('visited-by-submenu');

            $('ol#steps-customs')
                .find('li.parent-element')
                .removeClass('temp-active');

            $this
                .parents('li.parent-element')
                .addClass('active temp-active')
                .find('ul.sub-content')
                .removeClass('hidden');
            return;
        }

        function isFloat(n){
            return /(?: |^)\d*\.?\d+(?: |$)/.test(String(n));
        }
        function addListenerForSubMenus(CGuiObj) {
            $(CGuiObj.menuContainer)
                .find('ul.sub-content>li')
                .find('[data-canvas-menu-trigger]')
                .on('click', function(e) {
                    e.preventDefault();
                    CGuiObj.CanvasToolObj.showResetBtn();
                    var continueNextFlag = true;
                    // Hack for input field only
                    if ( $(this).parents('li.parent-element').data('config-name') !== undefined && $(this).parents('li.parent-element').data('config-name').toString().toLowerCase() == 'length') {
                        var inputLength = $(this).parents('li.parent-element').find('input#inputField').eq(0).val();
                        if (! $.isNumeric( inputLength)  || parseFloat(inputLength) <= 0 || inputLength.endsWith(".") ) {
                            continueNextFlag = false;
                            alert("Please enter a valid value to proceed");
                        }
                    }

                    if (continueNextFlag == true) {

                        if ($(this).parents('li.parent-element').next().is('li')) {
                            $('ol#steps-customs')
                                .find('li.parent-element')
                                .removeClass('temp-active active');
                        }

                        processAction($(this), CGuiObj);

                        var $thisElem = $(this);

                        // FOr Boot menu fix
                        if ($thisElem.hasClass('boot-color-trigger')){
                            $thisElem = $thisElem.parents('li').find('a').eq(0);
                        }

                        updateCurrentActiveMenu(CGuiObj, $(this).parents('li.parent-element'));
                        switchMenu(CGuiObj, $thisElem);
                        activateSubmenu($(this));

                        CGuiObj.CanvasToolObj.resetPrintBtn(CGuiObj);
                    }
                })
        }

        function activateSubmenu($this) {
            var subMenuActiveClass = 'active-subitem';
            var mainMenuVisitedClass = 'clicked-item';
            // Remove selected option from the current list of subcontents and all subsequent ones
            $this.parents('ul.sub-content').find('li').removeClass(subMenuActiveClass);
            $this.parents('li.parent-element').nextAll().find('ul.sub-content').find('li').removeClass(subMenuActiveClass);

            //Activate the active sub-menu
            $this.parent().addClass(subMenuActiveClass);

            // Acctivate the  active main menu
            $this.parents('li.parent-element').addClass(mainMenuVisitedClass);
            $this.parents('li.parent-element').nextAll().removeClass(mainMenuVisitedClass);
        }

        function toCamelCase(str) {
        	if (str === undefined)
        		str='';
            return str.replace(/\s+/g, '-').toLowerCase();
        }

        function updateSelectedOptions(GlobalCanvasObj, stringObj) {
            var newEntry = true;
            for (var i=0; i<GlobalCanvasObj.selectedOptions.length; i++) {
                if (GlobalCanvasObj.selectedOptions[i].key == stringObj.key.toLowerCase()) {
                    newEntry = false;
                    GlobalCanvasObj.selectedOptions[i] = stringObj;
                    GlobalCanvasObj.selectedOptions.length = i+1;
                    break;
                }
            }
            if (newEntry == true)
                GlobalCanvasObj.selectedOptions.push(stringObj);

            GlobalCanvasObj.CanvasToolObj.checkIfExceptions(GlobalCanvasObj);
            GlobalCanvasObj.CanvasToolObj.resetPrintBtn(GlobalCanvasObj);
        }


        function hideJacketTypeOption(selected_gt)
        {            
          //remove hidden class from li 
          $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').removeClass('custom-hidden');
          
          // add hidden class to required li only. 
          if(selected_gt == "sm (9/125um)")
          {
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="INDOOR PLENUM 250um"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="INDOOR PLENUM 900um"]').parent().addClass('custom-hidden');
          }
          if(selected_gt == "sm (9/125um) bbxs")
          {              
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="INDOOR PLENUM 250um"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="INDOOR PLENUM 900um"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="I/O PLENUM 4.8mm"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="I/O PVC 2mm"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="I/O PVC 3mm"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="INDOOR PLENUM DUPLEX (ROUND) 2mm"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="INDOOR PLENUM DUPLEX (ZIP) 2mm"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="INDOOR PVC 1.6mm"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="INDOOR PVC 3.0mm"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="INDOOR RISER 1.2mm"]').parent().addClass('custom-hidden');
          }            

          if(selected_gt == "mm om1 (62.5um)")
          {
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="INDOOR PLENUM 250um"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="INDOOR PLENUM 900um"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="I/O PLENUM 4.8mm"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="I/O PVC 2mm"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="I/O PVC 3mm"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="INDOOR PLENUM DUPLEX (ROUND) 2mm"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="INDOOR PLENUM DUPLEX (ZIP) 2mm"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="INDOOR PVC 1.6mm"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="INDOOR PLENUM 2.0mm"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="INDOOR RISER 1.2mm"]').parent().addClass('custom-hidden');
          }

          if(selected_gt == "mm om3 (50 um) - 10gig 300")
          {
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="INDOOR PLENUM 250um"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="INDOOR PLENUM 900um"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="I/O PLENUM 4.8mm"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="I/O PVC 2mm"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="I/O PVC 3mm"]').parent().addClass('custom-hidden');        
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="INDOOR PLENUM DUPLEX (ZIP) 2mm"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="INDOOR PVC 1.6mm"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="INDOOR RISER 1.2mm"]').parent().addClass('custom-hidden');
          }

          if(selected_gt == "mm om4 (10gig 550)")
          {                
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="INDOOR PLENUM 250um"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="INDOOR PLENUM 900um"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="I/O PLENUM 4.8mm"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="I/O PVC 2mm"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="I/O PVC 3mm"]').parent().addClass('custom-hidden');        
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="INDOOR PLENUM DUPLEX (ZIP) 2mm"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="INDOOR PVC 1.6mm"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="INDOOR RISER 1.2mm"]').parent().addClass('custom-hidden');
            $('ol#steps-customs').find('li[data-config-name="jacket_type"], ul,li').find('a[data-cgui-component-name="INDOOR PVC 3.0mm"]').parent().addClass('custom-hidden');
          }
        }

        function updateJacketTypeOptions(GlobalCanvasObj, stringObj)
        {
            var activeGrp =  GlobalCanvasObj.CanvasToolObj.activeGroup;
            if( activeGrp == "indoor jumpers/pigtails" || activeGrp == "test reference cords")
            {
                var selected_gt = stringObj.value.cguiComponentName.toLowerCase();
                hideJacketTypeOption(selected_gt);
            }
        }

        function updateFiberTypeOptions(GlobalCanvasObj, stringObj)
        {
            var activeGrp =  GlobalCanvasObj.CanvasToolObj.activeGroup;
            if( activeGrp == "indoor jumpers/pigtails" || activeGrp == "test reference cords")
            {
              var selection1 = GlobalCanvasObj.selectedOptions[0].value.cguiComponentName.toLowerCase();
              var selection2 = GlobalCanvasObj.selectedOptions[1].value.cguiComponentName;
              hideFiberTypeOptions(selection1, selection2);              
            }           
        }

        function hideFiberTypeOptions(selection1, selection2)
        {              
          //remove hidden class from li. 
          $('ol#steps-customs').find('li[data-config-name=\"fiber_count\"] ul li.custom-hidden').removeClass("custom-hidden");   

          // add hidden class to required li only. 
          if(selection1 == "sm (9/125um)" && (selection2 == "I/O PLENUM 4.8mm" || selection2 == "I/O PVC 2mm" || selection2 == "I/O PVC 3mm"))
          {
            $('ol#steps-customs').find('li[data-config-name="fiber_count"]').find('ul [data-cgui-component-name=DUPLEX]').parent().addClass('custom-hidden');
          }
          if(selection1 == "sm (9/125um)" && (selection2 == "INDOOR PVC 1.6mm"))
          {
            $('ol#steps-customs').find('li[data-config-name="fiber_count"]').find('ul [data-cgui-component-name=SIMPLEX]').parent().addClass('custom-hidden');
          }
          //Below code commented as only simplex option should be available for this combination of selection.
          /*if(selection1 == "mm om3 (50 um) - 10gig 300" && (selection2 == "INDOOR PVC 3.0mm"))
          {
            $('ol#steps-customs').find('li[data-config-name="fiber_count"]').find('ul [data-cgui-component-name=DUPLEX]').parent().addClass('custom-hidden');
          }*/
        }

        function updateCurrentActiveMenu($this, $elem) {
        	if ($elem === undefined)
        		$elem=false;
            if ($elem == false){
                $this.currentActiveMenu = this.$firstMenu;
                return;
            }

            if ($($elem).next().length != 0)
                $this.currentActiveMenu = $($elem).next();
        }

        /**
         * @return Object
         */
        function processDBConditions(CGuiObj) {
            // Get the wire color
            var definedDBConditions = CGuiObj.models.conditions.db;
            // Get the current cable id combination(#1 & #2) to work with for DBConditions OBJ
            if (CGuiObj.selectedOptions.length == 0)
                return {};

            var wireColor = undefined;
            var currentSelections = CGuiObj.selectedOptions;
            var glassTypeConditionsDataSet = false;
            var jacketTypeConditionsDataSet = false;

            /**
             * Selected Conds glassType, jacketType & FiberCount incase of other groups except for
             * Copper Products & MTP/MPO Cassettes
             *
             * For Copper Products Group
             * cableType & cableColor
             */
            var selectedConds = {
                glassType: '',
                jacketType: '',
                fiberCount: '',

                cableType: '',
                cableColor: '',
            };
            // Extracting the wire color selected
            var currentCableId;
            for (var i=0; i<currentSelections.length; i++) {
                // conds.push({key: currentSelections[i].key, value: currentSelections[i].value.context.dataset});
                if (currentSelections[i].key == 'glass_type') {
                    selectedConds.glassType = currentSelections[i].value.cguiComponentPartNumber;
                }
                if (currentSelections[i].key == 'jacket_type') {
                    selectedConds.jacketType = currentSelections[i].value.cguiComponentPartNumber;
                }

                if (currentSelections[i].key == 'fiber_count') {
                    selectedConds.fiberCount = currentSelections[i].value.cguiComponentPartNumber;
                }

                if (currentSelections[i].key == 'cable_type') {
                    selectedConds.cableType = currentSelections[i].value.cguiComponentPartNumber;
                }

                if (currentSelections[i].key == 'cable_color') {
                    selectedConds.cableColor = currentSelections[i].value.cguiComponentPartNumber;
                }
            }

            // Cable ID incase of copper group will be the combo of cableType & cableColor
            if (CGuiObj.CanvasToolObj.activeGroup.indexOf('copper') !== -1) {
                currentCableId = selectedConds.cableType +''+ selectedConds.cableColor;
            }
            else{
                // Processing the wire color by comparing selections with the predefined db conditions
                currentCableId = selectedConds.glassType +''+ selectedConds.jacketType +''+ selectedConds.fiberCount;
            }

            for (var i=0; i<CGuiObj.models.conditions.db.length; i++) {
                if (CGuiObj.models.conditions.db[i].cable_id.toString() == currentCableId) {
                    return CGuiObj.models.conditions.db[i];
                    break;
                }
            }
            return {};

        }


        /**
         * @return Object
         */
        function processStaticConditions(CGuiObj) {

            // Get the wire color
            var definedStaticConditions = CGuiObj.models.conditions.staticConds;
            // Get the current cable id combination(#1 & #2) to work with for DBConditions OBJ
            if (CGuiObj.selectedOptions.length == 0)
                return {};

            var wireColor = undefined;
            var currentSelections = CGuiObj.selectedOptions;
            var glassTypeConditionsDataSet = false;
            var jacketTypeConditionsDataSet = false;

            var currentSelectedConds = {
                glassType: '',
                jacketType: '',
                fiberCount: ''
            };

            var matchedCond = {
                connector: [],
                boot: [],
                cable: {
                    key: '',
                    cableColor: '',
                    fanoutColor: ''
                }
            };

            // Extracting the wire color selected
            // var currentCableId;
            for (var i=0; i<currentSelections.length; i++) {
                // conds.push({key: currentSelections[i].key, value: currentSelections[i].value.context.dataset});
                if (currentSelections[i].key == 'glass_type') {
                    currentSelectedConds.glassType = currentSelections[i].value.cguiComponentPartNumber;
                }
                if (currentSelections[i].key == 'jacket_type') {
                    currentSelectedConds.jacketType = get2D(currentSelections[i].value.cguiComponentPartNumber);
                }
                if (currentSelections[i].key == 'fiber_count') {
                    currentSelectedConds.fiberCount = currentSelections[i].value.cguiComponentPartNumber;
                }
            }

            // Processing the wire color by comparing selections with the predefined db conditions
            // currentCableId = currentSelectedConds.glassType +''+ currentSelectedConds.jacketType +''+ currentSelectedConds.fiberCount;

            var returnData = [];
            for (var i=0; i<CGuiObj.models.conditions.staticConds.length; i++) {

                // Continue-on
                // Connector conditions
                if (CGuiObj.models.conditions.staticConds[i].for == 'connector') {
                    for (var key in CGuiObj.models.conditions.staticConds[i]) {
                        var currentKeyAsArray = key.split('-');
                        // This is  a subtype-XXXCable|Fanout field
                        if (currentKeyAsArray.length > 1) {
                            // var cableKey = currentKeyAsArray[1].replace(pattern, '(\\d+)');
                            // var cableType = currentKeyAsArray[1].replace(pattern, '([\\w_]+)');
                            var cableKey = currentKeyAsArray[1].match(/\d+/g);
                            var connectorName = currentKeyAsArray[1].replace(/[0-9]/g, '');//match(/\w+/g);

                            cableKey = cableKey[0];
                            connectorName = connectorName.toLowerCase();
                            if ( cableKey == currentSelectedConds.glassType) {

                                matchedCond.connector.push({
                                    key: cableKey,
                                    connectorName: connectorName,
                                    color: CGuiObj.models.conditions.staticConds[i][key]});
                            }
                        }
                    }
                }

                // Wire conditions
                if (CGuiObj.models.conditions.staticConds[i].for == 'boot') {

                    for (var key in CGuiObj.models.conditions.staticConds[i]) {
                        var currentKeyAsArray = key.split('-');
                        // This is  a subtype-XXXCable|Fanout field
                        if (currentKeyAsArray.length > 1) {
                            var bootPartNumberKey = currentKeyAsArray[1].toString();

                            var color = CGuiObj.models.conditions.staticConds[i][key];

                            matchedCond.boot.push({
                                bootPartNumberKey: bootPartNumberKey,
                                color: color
                            });

                        }
                    }
                }

                // Reshufle all static boot conditions
                var reshuffleSetForBoots = [];
                if (matchedCond.boot.length > 0){
                    var uniquePartNumberKeys = [];

                    // Get the first record into unique partnumber keys
                    uniquePartNumberKeys.push(matchedCond.boot[0].bootPartNumberKey);
                    if (matchedCond.boot.length == 1){
                        reshuffleSetForBoots.push({bootPartNumberKey: matchedCond.boot[0].bootPartNumberKey, color: matchedCond.boot[0].color});
                    }
                    else{
                        for (var ik=1; ik<matchedCond.boot.length; ik++) {
                            if (uniquePartNumberKeys.indexOf(matchedCond.boot[ik].bootPartNumberKey) == -1) {
                                uniquePartNumberKeys.push(matchedCond.boot[ik].bootPartNumberKey);
                            }
                        }
                        // Unique set of partnumbers are added

                        for (var ik=0; ik<uniquePartNumberKeys.length; ik++) {
                            var bootPartNumberKey = uniquePartNumberKeys[ik];
                            var bootColors = [];

                            for (var j=0; j<matchedCond.boot.length; j++) {
                                if (bootPartNumberKey != matchedCond.boot[j].bootPartNumberKey)
                                    continue;
                                bootColors.push(matchedCond.boot[j].color);
                            }

                            reshuffleSetForBoots.push({bootPartNumberKey: bootPartNumberKey, color: bootColors});
                        }
                    }
                }
                matchedCond.boot = reshuffleSetForBoots;

                // Wire conditions
                if (CGuiObj.models.conditions.staticConds[i].for == 'cable') {
                    for (var key in CGuiObj.models.conditions.staticConds[i]) {
                        var currentKeyAsArray = key.split('-');
                        // This is  a subtype-XXXCable|Fanout field
                        if (currentKeyAsArray.length > 1) {
                            // var cableKey = currentKeyAsArray[1].replace(pattern, '(\\d+)');
                            // var cableType = currentKeyAsArray[1].replace(pattern, '([\\w_]+)');
                            var cableKey = currentKeyAsArray[1].match(/\d+/g);
                            var cableType = currentKeyAsArray[1].replace(/[0-9]/g, '').toLowerCase();

                            cableKey = cableKey[0];

                            if (cableKey == currentSelectedConds.glassType+currentSelectedConds.jacketType) {
                                    if (cableType == 'cable') {
                                        matchedCond.cable.key = cableKey;
                                        matchedCond.cable.cableColor = CGuiObj.models.conditions.staticConds[i][key];
                                    }

                                    if (cableType == 'fanout') {
                                        matchedCond.cable.key = cableKey;
                                        matchedCond.cable.fanoutColor = CGuiObj.models.conditions.staticConds[i][key];
                                    }
                                }

                        }
                    }
                }

            }
            return matchedCond;

        }

        function switchMenu(CGuiObj, $clickedElem, mainMenuClick) {
        	if (mainMenuClick === undefined)
    			mainMenuClick=false;
            // Hide the current active sub-menu and display the next one
            // Get the current active menu

            var $currentActiveMenu = CGuiObj.currentActiveMenu;

            // In-case the first element is selected
            // Reset all selections, and display the very first element
            if ($currentActiveMenu == '') {
                var $nextMenu = CGuiObj.$firstMenu;
            }
            else{
                //Find & SHow the next menu
                var $nextMenu = $currentActiveMenu;
            }
            if($nextMenu){
                // Hide all menus first
                CGuiObj.hideAll();
                if (mainMenuClick == false)
                    $nextMenu.addClass('active');

                $nextMenu.find('ul.sub-content').removeClass('hidden');
                $nextMenu.find('div.left-box').removeClass('hidden');

                // Processing all visited elements
                $nextMenu.nextAll('li.parent-element').removeClass('visited');
                $nextMenu.addClass('visited');

            }
            if ($clickedElem.parents('li.parent-element').next('li').length == 0) {
                $clickedElem.parents('li.parent-element').addClass('active visited')
                $clickedElem.parents('li.parent-element').find('ul.sub-content').addClass('hidden')
                $clickedElem.parents('li.parent-element').find('div.left-box').addClass('hidden');
            }

            // And update the next one

            // Make sure if the last menu is active, will be doing something
        }

        function processAction($subMenuClicked, CGuiObj) {
            $subMenuClickedParent = $subMenuClicked;
            var classList = '';
            var customSelectedBootColor = '';
            var breakoutLengthA = breakoutLengthB = breakoutLengthC = breakoutPartNumberA = breakoutPartNumberB = breakoutPartNumberC = '';
            var measure = '';

            if ($subMenuClicked.hasClass('breakout-option-trigger') === true){
                $subMenuClickedParent = undefined;
                $subMenuClickedParent = $subMenuClicked.parents('li.parent-element').find('a');
                breakoutLengthA = $subMenuClicked.parents('li.parent-element').find('ul.sub-content li').eq(0).find('select option:selected').val();
                breakoutPartNumberA = $subMenuClicked.parents('li.parent-element').find('ul.sub-content li').eq(0).find('select option:selected').data('part-number');

                breakoutLengthB = $subMenuClicked.parents('li.parent-element').find('ul.sub-content li').eq(1).find('select option:selected').val();
                breakoutPartNumberB = $subMenuClicked.parents('li.parent-element').find('ul.sub-content li').eq(1).find('select option:selected').data('part-number');

                breakoutLengthC = $subMenuClicked.parents('li.parent-element').find('ul.sub-content li').eq(2).find('select option:selected').val();
                breakoutPartNumberC = $subMenuClicked.parents('li.parent-element').find('ul.sub-content li').eq(2).find('select option:selected').data('part-number');
            }

            if ($subMenuClicked.hasClass('length-trigger') === true){
                $subMenuClickedParent = undefined;
                $subMenuClickedParent = $subMenuClicked.parent();
            }

            if ($subMenuClicked.hasClass('boot-color-trigger') === true){
                $subMenuClickedParent = undefined;
                $subMenuClickedParent = $subMenuClicked.parent().parent().find('a');

                classList = $subMenuClicked.attr('class').split(/\s+/);

                $.each(classList, function(index, item) {
                    if (item.endsWith("-btn")) {
                        customSelectedBootColor = item.split("-");
                        customSelectedBootColor = customSelectedBootColor[0];
                    }
                });
            }

            var label = $subMenuClickedParent
                            .data('cgui-component-name')
                            .toString()
                            .toUpperCase();

            var subMenuClickedDatasetObj = $subMenuClickedParent[0].dataset;

            var showLoaderUniqueKeyInitProcess = 'initProcess';
            CGuiObj.CanvasToolObj.showLoader(true, showLoaderUniqueKeyInitProcess);

            var canvasTextLeftIndex = canvasTextRightIndex = canvasTextBottomIndex = 0;
            switch ($subMenuClickedParent.data('config-name').toString().toLowerCase()) {

                case 'cable type':
                    var textIndexBottom = 0;
                    CGuiObj.CanvasToolObj.initCanvas();
                    CGuiObj.CanvasToolObj.clearCanvas();
                    updateSelectedOptions(CGuiObj, {key: "cable_type", value: subMenuClickedDatasetObj});

                    CGuiObj.CanvasToolObj.textLabels.bottom[textIndexBottom] = {key: 'cable_type', text: label};
                    CGuiObj.CanvasToolObj.textLabels.bottom.length = textIndexBottom+1;
                    CGuiObj.CanvasToolObj.textLabels.left.length = CGuiObj.CanvasToolObj.textLabels.right.length = 0;

                    CGuiObj.CanvasToolObj.redrawText();
                    CGuiObj.CanvasToolObj.resetBreakoutOptions();


                    // DO some magical stuff here
                    var cassetteImg = findFirstActiveImage($subMenuClickedParent);
                    CGuiObj.CanvasToolObj.drawCassetteBox(cassetteImg);
                    CGuiObj.CanvasToolObj.captureCanvasOutput('cable_type');

                    CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                    CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                    CGuiObj.CanvasToolObj.resetPartNumber(CGuiObj);
                    break;

                case 'cable color':
                    var textIndexBottom = 2;
                    CGuiObj.CanvasToolObj.initCanvas();
                    CGuiObj.CanvasToolObj.clearCanvas();
                    updateSelectedOptions(CGuiObj, {key: "cable_color", value: subMenuClickedDatasetObj});

                    CGuiObj.processDBConditions();

                    CGuiObj.CanvasToolObj.textLabels.bottom[textIndexBottom] = {key: 'cable_color', text: label};
                    CGuiObj.CanvasToolObj.textLabels.bottom.length = textIndexBottom+1;
                    CGuiObj.CanvasToolObj.textLabels.left.length = CGuiObj.CanvasToolObj.textLabels.right.length = 0;

                    CGuiObj.CanvasToolObj.redrawText();
                    CGuiObj.CanvasToolObj.resetBreakoutOptions();

                    var wireColor = label.toLowerCase().replace(' ', '');
                    if (wireColor != '') {
                        CGuiObj.CanvasToolObj.setWireColor(wireColor);
                    }
                    CGuiObj.CanvasToolObj.setWireType('simplex');
                    CGuiObj.CanvasToolObj.drawWire();

                    // DO some magical stuff here
                    var cassetteImg = findFirstActiveImage($subMenuClickedParent);
                    CGuiObj.CanvasToolObj.drawCassetteBox(cassetteImg);
                    CGuiObj.CanvasToolObj.captureCanvasOutput('cable_color');

                    CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                    CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                    CGuiObj.CanvasToolObj.resetPartNumber(CGuiObj);

                    // Applying DB COnditions for Copper Group
                    updateConnectorMenuByDBConditions(CGuiObj);
                    // Update menu image colors for boot types
                    updateBootMenuIcons();
                    break;

                case 'box type':
                    var textIndexBottom = 0;
                    CGuiObj.CanvasToolObj.initCanvas();
                    CGuiObj.CanvasToolObj.clearCanvas();
                    updateSelectedOptions(CGuiObj, {key: "box_type", value: subMenuClickedDatasetObj});


                    CGuiObj.CanvasToolObj.textLabels.bottom[textIndexBottom] = {key: 'box_type', text: label};
                    CGuiObj.CanvasToolObj.textLabels.bottom.length = textIndexBottom+1;
                    CGuiObj.CanvasToolObj.redrawText();
                    CGuiObj.CanvasToolObj.resetBreakoutOptions();


                    // DO some magical stuff here
                    var cassetteImg = findFirstActiveImage($subMenuClickedParent);
                    CGuiObj.CanvasToolObj.drawCassetteBox(cassetteImg);
                    CGuiObj.CanvasToolObj.captureCanvasOutput('box_type');

                    CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                    CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                    CGuiObj.CanvasToolObj.resetPartNumber(CGuiObj);
                    break;

                case 'glass type':
                    var textIndexBottom = 1;
                    CGuiObj.CanvasToolObj.initCanvas();
                    CGuiObj.CanvasToolObj.clearCanvas();
                    CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                    CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                    CGuiObj.CanvasToolObj.resetBreakoutOptions();

                    CGuiObj.CanvasToolObj.textLabels.bottom[textIndexBottom] = {key: 'glass_type', text: label};

                    updateSelectedOptions(CGuiObj, {key: "glass_type", value: subMenuClickedDatasetObj});


                    var type = 'box_type';
                    var image=false;
                    for (var i=0; i<CGuiObj.CanvasToolObj.history.length; i++) {
                        if (CGuiObj.CanvasToolObj.history[i].key == type && image == false){
                            image = CGuiObj.CanvasToolObj.history[i].image;
                        }
                    }
                    if (image != false) {
                        var canvasimageObj = new Image();
                        canvasimageObj.onload = function() {
                            var context = CGuiObj.CanvasToolObj.context;
                            x = 0;
                            y = 0;
                            context.drawImage(canvasimageObj, x, y);

                            CGuiObj.CanvasToolObj.textLabels.bottom.length = textIndexBottom+1;
                            CGuiObj.CanvasToolObj.textLabels.left.length = CGuiObj.CanvasToolObj.textLabels.right.length = 0;

                            CGuiObj.CanvasToolObj.redrawText();

                            CGuiObj.CanvasToolObj.captureCanvasOutput('glass_type');

                            CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                            CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                            CGuiObj.CanvasToolObj.resetPartNumber(CGuiObj);
                        };
                        canvasimageObj.src = image;
                    }
                    else
                    {
                        CGuiObj.CanvasToolObj.textLabels.bottom.length = textIndexBottom+1;
                        CGuiObj.CanvasToolObj.textLabels.left.length = CGuiObj.CanvasToolObj.textLabels.right.length = 0;

                        CGuiObj.CanvasToolObj.redrawText();

                        CGuiObj.CanvasToolObj.captureCanvasOutput('glass_type');

                        CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                        CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                        CGuiObj.CanvasToolObj.resetPartNumber(CGuiObj);
                    }
                    updateJacketTypeOptions(CGuiObj, {key: "glass_type", value: subMenuClickedDatasetObj});
                    break;

                /**
                 * Options for MTP-Cassettes
                 */
                case 'mpo connector':
                    var textIndexBottom = 2;
                    CGuiObj.CanvasToolObj.initCanvas();
                    CGuiObj.CanvasToolObj.clearCanvas();
                    CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                    CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                    CGuiObj.CanvasToolObj.textLabels.bottom[textIndexBottom] = {key: 'mpo-connector', text: label};

                    updateSelectedOptions(CGuiObj, {key: "mpo-connector", value: subMenuClickedDatasetObj});

                    var type = 'box_type';
                    var image=false;
                    for (var i=0; i<CGuiObj.CanvasToolObj.history.length; i++) {
                        if (CGuiObj.CanvasToolObj.history[i].key == type && image == false){
                            image = CGuiObj.CanvasToolObj.history[i].image;
                        }
                    }

                    var canvasimageObj = new Image();
                    canvasimageObj.onload = function() {
                        var context = CGuiObj.CanvasToolObj.context;
                        x = 0;
                        y = 0;
                        context.drawImage(canvasimageObj, x, y);

                        // DO some magical stuff here
                        var cassetteImg = findFirstActiveImage($subMenuClickedParent);
                        CGuiObj.CanvasToolObj.drawCassetteConnector(cassetteImg);
                        CGuiObj.CanvasToolObj.captureCanvasOutput('box_type');

                        CGuiObj.CanvasToolObj.textLabels.bottom.length = textIndexBottom+1;
                        CGuiObj.CanvasToolObj.textLabels.left.length = CGuiObj.CanvasToolObj.textLabels.right.length = 0;

                        CGuiObj.CanvasToolObj.redrawText();

                        CGuiObj.CanvasToolObj.captureCanvasOutput('glass_type');

                        CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                        CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                        CGuiObj.CanvasToolObj.resetPartNumber(CGuiObj);
                    };
                    canvasimageObj.src = image;
                    break;

                /**
                 * Hack options for MTP-Cassettes
                 */
                case 'output connector':
                    var textIndexBottom = 3;
                    //Add text
                    updateSelectedOptions(CGuiObj, {key: "output_connector", value: subMenuClickedDatasetObj});
                    CGuiObj.CanvasToolObj.textLabels.bottom[textIndexBottom] = {key: 'output_connector', text: label};
                    CGuiObj.CanvasToolObj.redrawText();
                    CGuiObj.CanvasToolObj.updatePrice(CGuiObj);
                    CGuiObj.CanvasToolObj.updateWeight(CGuiObj);
                    CGuiObj.CanvasToolObj.updatePartNumber(CGuiObj);
                    break;

                case 'jacket type':
                    var textIndexBottom = 2;
                    CGuiObj.CanvasToolObj.initCanvas();
                    CGuiObj.CanvasToolObj.clearCanvas();
                    CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                    CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                    CGuiObj.CanvasToolObj.resetBreakoutOptions();

                    updateSelectedOptions(CGuiObj, {key: "jacket_type", value: subMenuClickedDatasetObj});

                    CGuiObj.CanvasToolObj.textLabels.bottom[textIndexBottom]={key: 'jacket_type', text: label};

                    CGuiObj.CanvasToolObj.textLabels.bottom.length = textIndexBottom+1;
                    // console.log(CGuiObj.CanvasToolObj.textLabels.bottom, '*****');
                    CGuiObj.CanvasToolObj.textLabels.left.length = CGuiObj.CanvasToolObj.textLabels.right.length = 0;

                    CGuiObj.CanvasToolObj.redrawText();
                    CGuiObj.CanvasToolObj.captureCanvasOutput('jacket_type');

                    // Apply Static conds
                    CGuiObj.processStaticConditions();

                    CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                    CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                    CGuiObj.CanvasToolObj.resetPartNumber(CGuiObj);


                    var componentName = $subMenuClickedParent.data('cgui-component-name').toString().toLowerCase();
                    if ( componentName.indexOf('duplex') !== -1 )
                        CGuiObj.hideSimplexOption();
                    else
                        CGuiObj.showSimplexOption();

                    // Only simplex option should be available if jacket type is selected as "Indoor PVC 3.0mm"
                    if(componentName == "indoor pvc 3.0mm")
                    {
                        CGuiObj.showDuplexOption();
                        CGuiObj.hideSimplexOption();
                    }

                    updateConnectorMenuByStaticConditions(CGuiObj);
                    updateFiberTypeOptions(CGuiObj); 

                    break;

                case 'fiber count':
                    var textIndexBottom = 3;
                    var labelForCanvas = label;
                    CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                    CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                    CGuiObj.CanvasToolObj.resetBreakoutOptions();
                    updateSelectedOptions(CGuiObj, {key: "fiber_count", value: subMenuClickedDatasetObj});

                    var wireColor = '';
                    var fanoutColor = '';

                    if (CGuiObj.CanvasToolObj.activeConds.staticConds !== undefined && CGuiObj.CanvasToolObj.activeConds.staticConds.cable !== undefined && CGuiObj.CanvasToolObj.activeConds.staticConds.cable.key !== ''){
                        if (CGuiObj.CanvasToolObj.activeConds.staticConds.cable.cableColor.length != 0)
                            wireColor = CGuiObj.CanvasToolObj.activeConds.staticConds.cable.cableColor;
                        if (CGuiObj.CanvasToolObj.activeConds.staticConds.cable.fanoutColor.length != 0)
                            fanoutColor = CGuiObj.CanvasToolObj.activeConds.staticConds.cable.fanoutColor;
                    }

                    CGuiObj.processDBConditions();
                    if (CGuiObj.CanvasToolObj.activeConds.db.wire_color !== undefined)
                        wireColor = CGuiObj.CanvasToolObj.activeConds.db.wire_color;
                    if (CGuiObj.CanvasToolObj.activeConds.db.fanout_color !== undefined)
                        fanoutColor = CGuiObj.CanvasToolObj.activeConds.db.fanout_color;

                    CGuiObj.CanvasToolObj.clearCanvas();

                    // Testing wire mode, and redrawing the apt wire
                    var clickedWireType = $subMenuClickedParent.data('cgui-component-name').toString().toLowerCase();
                    switch (clickedWireType) {
                        case 'simplex':
                            if (wireColor != '') {
                                CGuiObj.CanvasToolObj.setWireColor(wireColor);
                            }
                            CGuiObj.CanvasToolObj.clearCanvas();
                            CGuiObj.CanvasToolObj.setWireType('simplex');
                            CGuiObj.CanvasToolObj.initCanvas();
                            CGuiObj.CanvasToolObj.drawWire();
                            break;
                        case 'duplex':
                            //drawing duplex wire
                            CGuiObj.CanvasToolObj.clearCanvas();
                            if (wireColor != '') {
                                CGuiObj.CanvasToolObj.setWireColor(wireColor);
                            }
                            CGuiObj.CanvasToolObj.setWireType('duplex');
                            CGuiObj.CanvasToolObj.initCanvas();
                            CGuiObj.CanvasToolObj.drawWire('duplex');
                            break;

                        case '4':
                            //fanouts-4
                            //drawing duplex wire
                            CGuiObj.CanvasToolObj.clearCanvas();
                            if (fanoutColor != '') {
                                CGuiObj.CanvasToolObj.setWireColor(fanoutColor);
                            }
                            CGuiObj.CanvasToolObj.setWireType('fanout', 4);
                            CGuiObj.CanvasToolObj.initCanvas();
                            CGuiObj.CanvasToolObj.drawWire('fanout', 4);

                            labelForCanvas = 'Fanout-4';
                            break;

                        case '6':
                            //fanouts-6
                            CGuiObj.CanvasToolObj.clearCanvas();
                            // Testing
                            if (fanoutColor != '') {
                                CGuiObj.CanvasToolObj.setWireColor(fanoutColor);
                            }
                            CGuiObj.CanvasToolObj.setWireType('fanout', 6);
                            CGuiObj.CanvasToolObj.initCanvas();
                            CGuiObj.CanvasToolObj.drawWire('fanout', 6);

                            labelForCanvas = 'Fanout-6';
                            break;

                        default:
                            //fanouts-n
                            CGuiObj.CanvasToolObj.clearCanvas();
                            if (fanoutColor != '') {
                                CGuiObj.CanvasToolObj.setWireColor(fanoutColor);
                            }
                            CGuiObj.CanvasToolObj.setWireType('fanout', parseInt(clickedWireType));
                            CGuiObj.CanvasToolObj.initCanvas();
                            CGuiObj.CanvasToolObj.drawWire('fanout', parseInt(clickedWireType));

                            labelForCanvas = 'Fanout-'+clickedWireType;
                            break;

                    }

                    // Adding label

                    CGuiObj.CanvasToolObj.textLabels.bottom[textIndexBottom] = {key: 'fiber_count', text: labelForCanvas};

                    CGuiObj.CanvasToolObj.textLabels.bottom.length = textIndexBottom+1;
                    CGuiObj.CanvasToolObj.textLabels.left.length = CGuiObj.CanvasToolObj.textLabels.right.length = 0;

                    CGuiObj.CanvasToolObj.redrawText();

                    CGuiObj.CanvasToolObj.captureCanvasOutput('fiber_count');


                    CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                    CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                    CGuiObj.CanvasToolObj.resetPartNumber(CGuiObj);

                    // These are custom static condtions,  and independent of the static condition
                    applyCustomStaticConditions(CGuiObj);

                    updateConnectorMenuByDBConditions(CGuiObj);
                    break;

                case 'node housing':
                    var textIndexBottom = 4;
                    CGuiObj.CanvasToolObj.initCanvas();
                    CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                    CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                    CGuiObj.CanvasToolObj.resetBreakoutOptions();
                    CGuiObj.CanvasToolObj.clearCanvas(false); // Wiping the canvas, without removing the coords details

                    var type = 'fiber_count';
                    var image=false;
                    for (var i=0; i<CGuiObj.CanvasToolObj.history.length; i++) {
                        if (CGuiObj.CanvasToolObj.history[i].key == type && image == false){
                            image = CGuiObj.CanvasToolObj.history[i].image;
                        }
                    }

                    var canvasimageObj = new Image();
                    canvasimageObj.onload = function () {
                        var context = CGuiObj.CanvasToolObj.context;
                        x = 0;
                        y = 0;
                        context.drawImage(canvasimageObj, x, y);

                        // Processing the name of the image component
                        var component_name = $subMenuClickedParent.data('cgui-component-name').toString().replace(/\s/g, '');
                        var color = 'green';

                        updateSelectedOptions(CGuiObj, {key: "node_housing", value: subMenuClickedDatasetObj});

                        CGuiObj.CanvasToolObj.textLabels.bottom[textIndexBottom] = {key: 'node_housing', text: label};

                        CGuiObj.CanvasToolObj.textLabels.bottom.length = textIndexBottom+1;
                        CGuiObj.CanvasToolObj.textLabels.left.length = 1;
                        CGuiObj.CanvasToolObj.textLabels.right.length = 0;

                        CGuiObj.CanvasToolObj.redrawText();


                        CGuiObj.CanvasToolObj.drawNodeHousing('side-a', 'nodehousing.png');
                        if (component_name.toLowerCase().indexOf('both') !== -1)
                            CGuiObj.CanvasToolObj.drawNodeHousing('side-b', 'nodehousing.png');

                        CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                        CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                        CGuiObj.CanvasToolObj.resetPartNumber(CGuiObj);

                        CGuiObj.CanvasToolObj.captureCanvasOutput('node_housing');

                    };
                    canvasimageObj.src = image;
                    CGuiObj.CanvasToolObj.showLoader(false, showLoaderUniqueKeyInitProcess);

                    break;

                case 'connector a':
                    var textIndexBottom = 5;
                    CGuiObj.CanvasToolObj.initCanvas();
                    CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                    CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                    CGuiObj.CanvasToolObj.resetBreakoutOptions();
                    CGuiObj.CanvasToolObj.clearCanvas(false); // Wiping the canvas, without removing the coords details
                    // console.log("Time to draw");
                    updateSelectedOptions(CGuiObj, {key: "connector_a", value: subMenuClickedDatasetObj});

                    // Testing capture canvas event
                    var type = 'fiber_count';
                    if (CGuiObj.CanvasToolObj.activeGroup.indexOf('node') !== -1)
                        type = 'node_housing';
                    if (CGuiObj.CanvasToolObj.activeGroup.indexOf('copper') !== -1)
                        type = 'cable_color';

                    var image=false;
                    for (var i=0; i<CGuiObj.CanvasToolObj.history.length; i++) {
                        if (CGuiObj.CanvasToolObj.history[i].key == type && image == false){
                            image = CGuiObj.CanvasToolObj.history[i].image;
                        }
                    }
                    var canvasimageObj = new Image();
                    canvasimageObj.onload = function () {
                        var context = CGuiObj.CanvasToolObj.context;
                        x = 0;
                        y = 0;
                        context.drawImage(canvasimageObj, x, y);

                        // Processing the name of the image component
                        var component_name = $subMenuClickedParent.data('cgui-component-name').toString().replace(/\s/g, '');
                        var color = 'green';

                        CGuiObj.CanvasToolObj.textLabels.left[0] = {key: 'connector_a', text: label};

                        CGuiObj.CanvasToolObj.textLabels.bottom.length = textIndexBottom+1;
                        CGuiObj.CanvasToolObj.textLabels.left.length = 1;
                        CGuiObj.CanvasToolObj.textLabels.right.length = 0;

                        CGuiObj.CanvasToolObj.redrawText();

                        var connectorImage = findFirstActiveImage($subMenuClickedParent);

                        var connectorHack = false;
                        if ( connectorImage !== undefined && typeof(connectorImage) == 'string' && connectorImage.toLowerCase().indexOf('e2000') !== -1) {
                            connectorHack = true;
                        }

                        CGuiObj.CanvasToolObj.drawConnectors("left", connectorImage, connectorHack, {status: false, componentKey: 'connector_a'});//, img[0].image);

                        CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                        CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                        CGuiObj.CanvasToolObj.resetPartNumber(CGuiObj);

                        CGuiObj.CanvasToolObj.captureCanvasOutput('connector_a');

                    };
                    canvasimageObj.src = image;
                    CGuiObj.CanvasToolObj.showLoader(false, showLoaderUniqueKeyInitProcess);

                    break;

                case 'pinout a':
                    var textIndexBottom = 5;
                    CGuiObj.CanvasToolObj.initCanvas();
                    CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                    CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                    CGuiObj.CanvasToolObj.clearCanvas(false);
                    CGuiObj.CanvasToolObj.resetBreakoutOptions();

                    updateSelectedOptions(CGuiObj, {key: "pinout_a", value: subMenuClickedDatasetObj});

                    var type = 'connector_a';

                    var image=false;
                    for (var i=0; i<CGuiObj.CanvasToolObj.history.length; i++) {
                        if (CGuiObj.CanvasToolObj.history[i].key == type && image == false){
                            image = CGuiObj.CanvasToolObj.history[i].image;
                        }
                    }
                    var canvasimageObj = new Image();
                    canvasimageObj.onload = function () {
                        var context = CGuiObj.CanvasToolObj.context;
                        x = 0;
                        y = 0;
                        context.drawImage(canvasimageObj, x, y);

                        var component_name = $subMenuClickedParent.data('cgui-component-name').toLowerCase();

                        //Add text
                        CGuiObj.CanvasToolObj.textLabels.left[1] = {key: 'pinout_a', text: label};

                        CGuiObj.CanvasToolObj.textLabels.right.length = 0;
                        CGuiObj.CanvasToolObj.textLabels.left.length = 2;
                        CGuiObj.CanvasToolObj.textLabels.bottom.length = textIndexBottom+1;

                        CGuiObj.CanvasToolObj.redrawText();

                        CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                        CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                        CGuiObj.CanvasToolObj.resetPartNumber(CGuiObj);
                        CGuiObj.CanvasToolObj.captureCanvasOutput('pinout_a');

                    };
                    canvasimageObj.src = image;
                    break;

                case 'boot type a':
                    var textIndexBottom = 5;
                    CGuiObj.CanvasToolObj.initCanvas();
                    CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                    CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                    CGuiObj.CanvasToolObj.clearCanvas(false);
                    CGuiObj.CanvasToolObj.resetBreakoutOptions();

                    updateSelectedOptions(CGuiObj, {key: "boot_type_a", value: subMenuClickedDatasetObj});

                    var type = 'connector_a';
                    var image=false;
                    for (var i=0; i<CGuiObj.CanvasToolObj.history.length; i++) {
                        if (CGuiObj.CanvasToolObj.history[i].key == type && image == false){
                            image = CGuiObj.CanvasToolObj.history[i].image;
                        }
                    }
                    var canvasimageObj = new Image();
                    canvasimageObj.onload = function () {
                        var context = CGuiObj.CanvasToolObj.context;
                        x = 0;
                        y = 0;
                        context.drawImage(canvasimageObj, x, y);

                        var drawFlag = true;

                        //Add Text
                        CGuiObj.CanvasToolObj.textLabels.left[2] = {key: 'boot-type-a', text: label};

                        CGuiObj.CanvasToolObj.textLabels.left.length = 3;
                        CGuiObj.CanvasToolObj.textLabels.right.length = 0;

                        CGuiObj.CanvasToolObj.textLabels.bottom.length = textIndexBottom+1;

                        CGuiObj.CanvasToolObj.redrawText();
                        var image_uri = $subMenuClickedParent.data('data-cgui-img-uri');


                        var bootImage = undefined;
                        if (label.toLowerCase().indexOf('none') == -1) {
                            var bootMetaObj = $subMenuClickedParent.data('canvas-image');
                            var bootImage = [];
                            if (bootMetaObj.length > 0 && bootMetaObj !== null) {
                                bootImage = bootMetaObj[0].img;
                                bootImage = findFirstActiveImage($subMenuClickedParent);
                            }
                        }

                        if (label.toLowerCase().indexOf('none') == -1) {
                            // setting up the name
                            var bootNameArr = bootImage.split("_");
                            var bootName = '';
                            for (var i=0; i<bootNameArr.length-1; i++) {
                                bootName += bootNameArr[i]+"_";
                            }
                            var origColorWithExt = bootNameArr[bootNameArr.length-1].split(".");

                            if (customSelectedBootColor != "")
                                bootName +=  customSelectedBootColor+"."+origColorWithExt[1];
                            else
                                bootName +=  bootNameArr[bootNameArr.length-1];

                            CGuiObj.CanvasToolObj.drawBoots("left", bootName, {status: false, componentKey: 'boot_type_a'}, drawFlag);
                        }

                        CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                        CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                        CGuiObj.CanvasToolObj.resetPartNumber(CGuiObj);
                        CGuiObj.CanvasToolObj.captureCanvasOutput('boot_type_a');

                    };
                    canvasimageObj.src = image;
                    break;

                case 'connector b':
                    var textIndexBottom = 5;
                    CGuiObj.CanvasToolObj.initCanvas();
                    CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                    CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                    CGuiObj.CanvasToolObj.clearCanvas(false);
                    CGuiObj.CanvasToolObj.resetBreakoutOptions();

                    updateSelectedOptions(CGuiObj, {key: "connector_b", value: subMenuClickedDatasetObj});


                    var type = 'connector_a';
                    if (CGuiObj.CanvasToolObj.activeGroup.indexOf('copper') !== -1)
                        type = 'boot_type_a';

                    var image=false;
                    for (var i=0; i<CGuiObj.CanvasToolObj.history.length; i++) {
                        if (CGuiObj.CanvasToolObj.history[i].key == type && image == false){
                            image = CGuiObj.CanvasToolObj.history[i].image;
                        }
                    }
                    var canvasimageObj = new Image();
                    canvasimageObj.onload = function () {
                        var context = CGuiObj.CanvasToolObj.context;
                        x = 0;
                        y = 0;
                        context.drawImage(canvasimageObj, x, y);

                        var component_name = $subMenuClickedParent.data('cgui-component-name').toLowerCase();

                        //Add text
                        CGuiObj.CanvasToolObj.textLabels.right[0] = {key: 'connector_b', text: label};

                        if (CGuiObj.CanvasToolObj.activeGroup.indexOf('copper') !== -1) {
                            CGuiObj.CanvasToolObj.textLabels.right.length = 1;
                            CGuiObj.CanvasToolObj.textLabels.left.length = 3;
                            CGuiObj.CanvasToolObj.textLabels.bottom.length = textIndexBottom+1;
                        }
                        else {
                            CGuiObj.CanvasToolObj.textLabels.right.length = 1;
                            CGuiObj.CanvasToolObj.textLabels.left.length = 1;
                            CGuiObj.CanvasToolObj.textLabels.bottom.length = textIndexBottom+1;
                        }

                        CGuiObj.CanvasToolObj.redrawText();

                        if (component_name != 'pigtail') {
                            var connectorImage = findFirstActiveImage($subMenuClickedParent);

                            var connectorHack = false;
                            if ( connectorImage !== undefined && connectorImage.toLowerCase().indexOf('e2000') !== -1) {
                                connectorHack = true;
                            }

                            CGuiObj.CanvasToolObj.drawConnectors('right', connectorImage, connectorHack, {status: false, componentKey: 'connector_b'});
                        }
                        else {
                            // Remove extra wire edges in-case of fanouts
                            CGuiObj.CanvasToolObj.clearSideWires('right');
                            CGuiObj.CanvasToolObj.updateDrawnConnectors('right', {x: 0, y: 0, w: 0, h: 0 });

                        }

                        CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                        CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                        CGuiObj.CanvasToolObj.resetPartNumber(CGuiObj);
                        CGuiObj.CanvasToolObj.captureCanvasOutput('connector_b');

                    };
                    canvasimageObj.src = image;

                    break;

                case 'pinout b':
                    var textIndexBottom = 5;
                    CGuiObj.CanvasToolObj.initCanvas();
                    CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                    CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                    CGuiObj.CanvasToolObj.clearCanvas(false);
                    CGuiObj.CanvasToolObj.resetBreakoutOptions();

                    updateSelectedOptions(CGuiObj, {key: "pinout_b", value: subMenuClickedDatasetObj});

                    var type = 'connector_b';

                    var image=false;
                    for (var i=0; i<CGuiObj.CanvasToolObj.history.length; i++) {
                        if (CGuiObj.CanvasToolObj.history[i].key == type && image == false){
                            image = CGuiObj.CanvasToolObj.history[i].image;
                        }
                    }
                    var canvasimageObj = new Image();
                    canvasimageObj.onload = function () {
                        var context = CGuiObj.CanvasToolObj.context;
                        x = 0;
                        y = 0;
                        context.drawImage(canvasimageObj, x, y);

                        var component_name = $subMenuClickedParent.data('cgui-component-name').toLowerCase();

                        //Add text
                        CGuiObj.CanvasToolObj.textLabels.right[1] = {key: 'pinout_b', text: label};

                        CGuiObj.CanvasToolObj.textLabels.left.length = 3;
                        CGuiObj.CanvasToolObj.textLabels.right.length = 2;
                        CGuiObj.CanvasToolObj.textLabels.bottom.length = textIndexBottom+1;

                        CGuiObj.CanvasToolObj.redrawText();

                        CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                        CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                        CGuiObj.CanvasToolObj.resetPartNumber(CGuiObj);
                        CGuiObj.CanvasToolObj.captureCanvasOutput('pinout_a');

                    };
                    canvasimageObj.src = image;
                    break;

                case 'boot type b':
                    var textIndexBottom = 5;
                    CGuiObj.CanvasToolObj.initCanvas();
                    CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                    CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                    CGuiObj.CanvasToolObj.clearCanvas(false);
                    CGuiObj.CanvasToolObj.resetBreakoutOptions();

                    updateSelectedOptions(CGuiObj, {key: "boot_type_b", value: subMenuClickedDatasetObj});

                    var type = 'connector_b';
                    var image=false;
                    for (var i=0; i<CGuiObj.CanvasToolObj.history.length; i++) {
                        if (CGuiObj.CanvasToolObj.history[i].key == type && image == false){
                            image = CGuiObj.CanvasToolObj.history[i].image;
                        }
                    }
                    var canvasimageObj = new Image();
                    canvasimageObj.onload = function () {
                        var context = CGuiObj.CanvasToolObj.context;
                        x = 0;
                        y = 0;
                        context.drawImage(canvasimageObj, x, y);

                        var drawFlag = true;
                        var component_name = $subMenuClickedParent.data('cgui-component-name').toLowerCase();
                        for (var i=0; i<CGuiObj.selectedOptions.length; i++) {
                            if (CGuiObj.selectedOptions[i].key == 'connector_b') {
                                if (CGuiObj.selectedOptions[i].value.cguiComponentName.toLowerCase() == 'pigtail'){
                                    drawFlag = false;
                                    break;
                                }
                            }
                        }


                        //Add Text
                        CGuiObj.CanvasToolObj.textLabels.right[2] = {key: 'boot-type-a', text: label};

                        CGuiObj.CanvasToolObj.textLabels.bottom.length = textIndexBottom+1;

                        CGuiObj.CanvasToolObj.redrawText();
                        var image_uri = $subMenuClickedParent.data('data-cgui-img-uri');

                        var bootMetaObj = $subMenuClickedParent.data('canvas-image');
                        var bootImage = undefined;
                        if (label.toLowerCase().indexOf('none') == -1) {
                            var bootImage = [];
                            if (bootMetaObj.length > 0 && bootMetaObj !== null) {
                                bootImage = bootMetaObj[0].img;
                                bootImage = findFirstActiveImage($subMenuClickedParent);
                            }
                        }

                        if (label.toLowerCase().indexOf('none') == -1) {
                            // setting up the name
                            var bootNameArr = bootImage.split("_");
                            var bootName = '';
                            for (var i=0; i<bootNameArr.length-1; i++) {
                                bootName += bootNameArr[i]+"_";
                            }
                            var origColorWithExt = bootNameArr[bootNameArr.length-1].split(".");

                            if (customSelectedBootColor != "")
                                bootName +=  customSelectedBootColor+"."+origColorWithExt[1];
                            else
                                bootName +=  bootNameArr[bootNameArr.length-1];
                            CGuiObj.CanvasToolObj.drawBoots("right", bootName, {status: false, componentKey: 'boot_type_b'}, drawFlag);
                        }

                        CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                        CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                        CGuiObj.CanvasToolObj.resetPartNumber(CGuiObj);
                        CGuiObj.CanvasToolObj.captureCanvasOutput('boot_type_b');

                    };
                    canvasimageObj.src = image;
                    break;

                case 'boot type':
                    var textIndexBottom = 5;
                    CGuiObj.CanvasToolObj.initCanvas();
                    CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                    CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                    CGuiObj.CanvasToolObj.clearCanvas(false);
                    CGuiObj.CanvasToolObj.resetBreakoutOptions();

                    updateSelectedOptions(CGuiObj, {key: "boot_type", value: subMenuClickedDatasetObj});

                    var type = 'connector_b';
                    var image=false;
                    for (var i=0; i<CGuiObj.CanvasToolObj.history.length; i++) {
                        if (CGuiObj.CanvasToolObj.history[i].key == type && image == false){
                            image = CGuiObj.CanvasToolObj.history[i].image;
                        }
                    }
                    var canvasimageObj = new Image();
                    canvasimageObj.onload = function () {
                        var context = CGuiObj.CanvasToolObj.context;
                        x = 0;
                        y = 0;
                        context.drawImage(canvasimageObj, x, y);

                        var drawFlag = true;
                        var component_name = $subMenuClickedParent.data('cgui-component-name').toLowerCase();
                        for (var i=0; i<CGuiObj.selectedOptions.length; i++) {
                            if (CGuiObj.selectedOptions[i].key == 'connector_b') {
                                if (CGuiObj.selectedOptions[i].value.cguiComponentName.toLowerCase() == 'pigtail'){
                                    drawFlag = false;
                                    break;
                                }
                            }
                        }


                        //Add Text
                        CGuiObj.CanvasToolObj.textLabels.left[1] = {key: 'boot-a', text: label};


                        if (CGuiObj.CanvasToolObj.exceptions.pigtail.right == false) {
                            CGuiObj.CanvasToolObj.textLabels.right[1] = {key: 'boot-b', text: label};
                        }
                        else{
                            CGuiObj.CanvasToolObj.textLabels.right[1] = {key: 'boot-b', text: ''};
                        }

                        //Update Boot Label in case of APC Conn
						var label_text = JSON.parse(subMenuClickedDatasetObj.canvasImage);
						var activeGroup = CGui.CanvasToolObj.activeGroup;
						if(activeGroup == "catv node cables")
						{
							if(CGui.selectedOptions[4].value.cguiComponentName.indexOf('APC') !== -1)
							{
								for (var key in label_text) 
								{
									var img_name = label_text[key]['img'];
									var img_status = label_text[key]['status'];							
									if(img_name.indexOf("green") !== -1 && img_status == 1)
									{										
										CGuiObj.CanvasToolObj.textLabels.left[1] = {key: 'boot-a', text: label};
									} else
									{									
										CGuiObj.CanvasToolObj.textLabels.left[1] = {key: 'boot-a', text: "RIBBED"};
									}
								}
							}
							
							if(CGui.selectedOptions[5].value.cguiComponentName.indexOf('APC') !== -1)
							{
								for (var key in label_text) 
								{
									var img_name = label_text[key]['img'];
									var img_status = label_text[key]['status'];																		
									if(img_name.indexOf("green") !== -1 && img_status == 1)
									{										
										CGuiObj.CanvasToolObj.textLabels.right[1] = {key: 'boot-b', text: label};
									} else
									{									
										CGuiObj.CanvasToolObj.textLabels.right[1] = {key: 'boot-b', text: "RIBBED"};
									}
								}
							}
						}
						else
						{
							if(CGui.selectedOptions[3].value.cguiComponentName.indexOf('APC') !== -1)
							{
								for (var key in label_text) 
								{
									var img_name = label_text[key]['img'];
									var img_status = label_text[key]['status'];																		
									if(img_name.indexOf("green") !== -1 && img_status == 1)
									{										
										CGuiObj.CanvasToolObj.textLabels.left[1] = {key: 'boot-a', text: label};
									} else
									{									
										CGuiObj.CanvasToolObj.textLabels.left[1] = {key: 'boot-a', text: "RIBBED"};
									}
								}
							}
							
							if(CGui.selectedOptions[4].value.cguiComponentName.indexOf('APC') !== -1)
							{
								for (var key in label_text) 
								{
									var img_name = label_text[key]['img'];
									var img_status = label_text[key]['status'];																		
									if(img_name.indexOf("green") !== -1 && img_status == 1)
									{
										CGuiObj.CanvasToolObj.textLabels.right[1] = {key: 'boot-b', text: label};
									} else
									{									
										CGuiObj.CanvasToolObj.textLabels.right[1] = {key: 'boot-b', text: "RIBBED"};
									}
								}
							}
						}

                        CGuiObj.CanvasToolObj.textLabels.right[3] = CGuiObj.CanvasToolObj.textLabels.right[2] = CGuiObj.CanvasToolObj.textLabels.left[3] = CGuiObj.CanvasToolObj.textLabels.left[2] = {};

                        CGuiObj.CanvasToolObj.textLabels.bottom.length = textIndexBottom+1;

                        CGuiObj.CanvasToolObj.redrawText();
                        var image_uri = $subMenuClickedParent.data('data-cgui-img-uri');

                        var bootMetaObj = $subMenuClickedParent.data('canvas-image');
                        var bootImage = [];
                        if (bootMetaObj.length > 0) {
                            bootImage = bootMetaObj[0].img;
                        }else{
                            bootImage = undefined;
                        }

                        bootImage = findFirstActiveImage($subMenuClickedParent);


                        // setting up the name
                        var bootNameArr = bootImage.split("_");
                        var bootName = '';
                        for (var i=0; i<bootNameArr.length-1; i++) {
                            bootName += bootNameArr[i]+"_";
                        }
                        var origColorWithExt = bootNameArr[bootNameArr.length-1].split(".");

                        if (customSelectedBootColor != "")
                            bootName +=  customSelectedBootColor+"."+origColorWithExt[1];
                        else
                            bootName +=  bootNameArr[bootNameArr.length-1];

                        CGuiObj.CanvasToolObj.drawBoots("left", bootName, {status: false, componentKey: 'boot_type'}, drawFlag, subMenuClickedDatasetObj);

                        CGuiObj.CanvasToolObj.drawBoots('right', bootName, {status: false, componentKey: 'boot_type'}, drawFlag, subMenuClickedDatasetObj);

                        CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                        CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                        CGuiObj.CanvasToolObj.resetPartNumber(CGuiObj);
                        CGuiObj.CanvasToolObj.captureCanvasOutput('boot_type');

                    };
                    canvasimageObj.src = image;
                    break;
                /*case 'length':
                    var inputLength = $subMenuClickedParent.parents('a').find('input[type=text]').eq(0).val();
                    $subMenuClickedParentAnchorTag = $subMenuClickedParent.parents('a').eq(0);
                    $subMenuClickedDataset = $subMenuClickedParentAnchorTag[0].dataset;
                    $subMenuClickedDataset['userInput'] = inputLength;
                    // CGuiObj.CanvasToolObj.resetPrice(CGuiObj);

                    updateSelectedOptions(CGuiObj, {key: "length", value: $subMenuClickedDataset});
                    CGuiObj.CanvasToolObj.resetPartNumber(CGuiObj);
                    break;*/
                case 'length':
                    var inputLength = $subMenuClickedParent.parents('li.parent-element').find('input#inputField').eq(0).val();
                    var unitSelected = $subMenuClickedParent.parents('li.parent-element').find('select.length option:selected').val();

                    var unitPartNumberSelected = $subMenuClickedParent.parents('li.parent-element').find('select.length option:selected').data('part-number');

                    var measurePartNumberSelected = $subMenuClickedParent.parents('li.parent-element').find('select.length option:selected').text();

                    // $subMenuClickedParentAnchorTag = $subMenuClickedParent.parents('li.parent-element').find('li.custom-btnsec>a').eq(0);
                    // console.log(subMenuClickedDatasetObj, "Is the sub menu clicked parent", inputLength);
                    // $subMenuClickedDataset = $subMenuClickedParent.parent().dataset;
                    // // $subMenuClickedDataset = {};
                    subMenuClickedDatasetObj['unitSelected'] = unitSelected;
                    subMenuClickedDatasetObj['userInput'] = inputLength;
                    subMenuClickedDatasetObj['unitPartNumberSelected'] = unitPartNumberSelected;
                    subMenuClickedDatasetObj['measurePartNumberSelected'] = measurePartNumberSelected;
                    // CGuiObj.CanvasToolObj.resetPrice(CGuiObj);

                    updateSelectedOptions(CGuiObj, {key: "length", value: subMenuClickedDatasetObj});

                    CGuiObj.CanvasToolObj.resetPartNumber(CGuiObj);

                    CGuiObj.CanvasToolObj.drawLengthLine(CGuiObj);
                    CGuiObj.CanvasToolObj.updatePrice(CGuiObj);
                    CGuiObj.CanvasToolObj.updateWeight(CGuiObj);
                    CGuiObj.CanvasToolObj.updatePartNumber(CGuiObj);

                    // Update discount amount to be displayed throughout, only if price is active
                    // if (CGuiObj.CanvasToolObj.isPriceActive)
                    //     updateSelectedOptions(CGuiObj, {key: "user_discount", value: {configName: 'Discount', cguiComponentName: '$'+CGuiObj.CanvasToolObj.user_discount.toString()}});
                    break;

                case 'polarity':
                    var textIndexBottom = 6;

                    CGuiObj.CanvasToolObj.initCanvas();
                    CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                    CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                    CGuiObj.CanvasToolObj.resetBreakoutOptions();

                    //Add text
                    updateSelectedOptions(CGuiObj, {key: "polarity", value: subMenuClickedDatasetObj});
                    CGuiObj.CanvasToolObj.textLabels.bottom[textIndexBottom] = {key: 'polarity', text: label};

                    CGuiObj.CanvasToolObj.textLabels.left.length = 2;
                    CGuiObj.CanvasToolObj.textLabels.right.length = 2;
                    CGuiObj.CanvasToolObj.redrawText();
                    CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                    CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                    CGuiObj.CanvasToolObj.resetPartNumber(CGuiObj);
                    break;

                case 'breakout options':
                    CGuiObj.CanvasToolObj.initCanvas();
                    CGuiObj.CanvasToolObj.resetPrice(CGuiObj);
                    CGuiObj.CanvasToolObj.resetWeight(CGuiObj);
                    CGuiObj.CanvasToolObj.resetPartNumber(CGuiObj);
                    CGuiObj.CanvasToolObj.clearCanvas(false);

                    var type = 'boot_type';
                    var image=false;
                    for (var i=0; i<CGuiObj.CanvasToolObj.history.length; i++) {
                        if (CGuiObj.CanvasToolObj.history[i].key == type && image == false){
                            image = CGuiObj.CanvasToolObj.history[i].image;
                        }
                    }
                    // console.log("Breakout options")
                    var canvasimageObj = new Image();
                    canvasimageObj.onload = function () {
                        var context = CGuiObj.CanvasToolObj.context;
                        x = 0;
                        y = 0;
                        context.drawImage(canvasimageObj, x, y);

                        var addOnMeta = subMenuClickedDatasetObj;
                        addOnMeta['partNumberSideA'] =  breakoutPartNumberA;
                        addOnMeta['partNumberSideB'] = breakoutPartNumberB;
                        addOnMeta['partNumberFurcation'] = breakoutPartNumberC;

                        updateSelectedOptions(CGuiObj, {key: "breakout_options", value: addOnMeta});

                        labelA = 'Breakout-'+ breakoutLengthA;
                        labelB = 'Breakout-'+ breakoutLengthB;
                        labelC = 'Breakout Furcation-'+ breakoutLengthC;

                        CGuiObj.CanvasToolObj.textLabels.left[2] = {key: 'breakout_options', text: labelA};

                        // Display Only if not Pigtail
                        if (CGuiObj.CanvasToolObj.exceptions.pigtail.right == false) {
                            CGuiObj.CanvasToolObj.textLabels.right[2] = {key: 'breakout_options', text: labelB};
                        }
                        else
                        {
                            CGuiObj.CanvasToolObj.textLabels.right[2] = {key: 'breakout_options', text: ''};

                        }

                        CGuiObj.CanvasToolObj.textLabels.left[3] = {key: 'breakout_options', text: labelC};

                        if (CGuiObj.CanvasToolObj.exceptions.pigtail.right == false) {
                            CGuiObj.CanvasToolObj.textLabels.right[3] = {key: 'breakout_options', text: labelC};
                        }
                        else
                        {
                            CGuiObj.CanvasToolObj.textLabels.right[3] = {key: 'breakout_options', text: ''};
                        }

                        CGuiObj.CanvasToolObj.redrawText();
                    }
                    canvasimageObj.src = image;
                    break;

                case 'options':
                    CGuiObj.CanvasToolObj.initCanvas();
                    var clickedOptionComponentName = $subMenuClickedParent.data('cgui-component-name').toLowerCase();

                    var noWrite = false;
                    var leftWrite = false;
                    var rightWrite = false;
                    var bothWrite = false
                    // Check for the custom option clicked
                    // Incase of none or No-Duplex Clips do-nothing

                    if (clickedOptionComponentName.indexOf('none') !== -1 || clickedOptionComponentName.indexOf('no duplex clips') !== -1) {
                        noWrite = true;
                    }
                    else {
                        // Perform operation for pulling eye
                        if ( clickedOptionComponentName.indexOf('pulling') !== -1) {
                            // Check for left or right or no drawing
                            if (clickedOptionComponentName.indexOf('both') !== -1) {
                                bothWrite = true;
                            }
                            if (clickedOptionComponentName.indexOf('side a') !== -1) {
                                leftWrite = true;
                            }

                            if (clickedOptionComponentName.indexOf('side b') !== -1) {
                                rightWrite = true;
                            }
                        }
                        if (clickedOptionComponentName.indexOf('duplex clips') !== -1) {
                            if (clickedOptionComponentName.indexOf('no') !== -1) {
                                noWrite = true;
                            }
                            else
                                bothWrite = true;
                        }
                    }

                    // Resetting the default text
                    CGuiObj.CanvasToolObj.textLabels.left[4] = CGuiObj.CanvasToolObj.textLabels.right[4] = {};
                    if (!noWrite) {
                        if (bothWrite) {
                            CGuiObj.CanvasToolObj.textLabels.left[4] = {key: 'options', text: clickedOptionComponentName};
                            CGuiObj.CanvasToolObj.textLabels.right[4] = {key: 'options', text: clickedOptionComponentName};
                        }

                        if (leftWrite) {
                            CGuiObj.CanvasToolObj.textLabels.left[4] = {key: 'options', text: clickedOptionComponentName};
                        }

                        if (rightWrite) {
                            CGuiObj.CanvasToolObj.textLabels.right[4] = {key: 'options', text: clickedOptionComponentName};
                        }

                    }
                    CGuiObj.CanvasToolObj.redrawText();

                    updateSelectedOptions(CGuiObj, {key: "options", value: subMenuClickedDatasetObj});
                    break;
            }
            CGuiObj.CanvasToolObj.showLoader(false, showLoaderUniqueKeyInitProcess);
        }

        /**
         * Canvas Tool- wrapper functions
         */

        function canvas_init(CanvasToolObj) {
            CanvasToolObj.initCanvas();

        }

        function canvas_clear(CanvasToolObj) {
            CanvasToolObj.clearCanvas();

        }

        function currentSelectionSetForConditions(CGuiObj, min) {
        	if (min === undefined)
        		min=3;
            var currentConditionsSet = {
                glassType: '',
                jacketType: '',
                fiberCount: '',
                misc: []
            }

            if (currentConditionsSet.length < min-1)
                return false;
            // Get the current wire combo selected
            for (var i=0; i<CGuiObj.selectedOptions.length; i++) {

                var currentType = CGuiObj.selectedOptions[i].key;
                switch (currentType){
                    case 'glass_type':
                        currentConditionsSet.glassType = CGuiObj.selectedOptions[i].value.cguiComponentPartNumber;
                        break;

                    case 'jacket_type':
                        currentConditionsSet.jacketType = CGuiObj.selectedOptions[i].value.cguiComponentPartNumber;
                        break;

                    case 'fiber_count':
                        currentConditionsSet.fiberCount = CGuiObj.selectedOptions[i].value.cguiComponentPartNumber;
                        break;

                    case 'length':
                        unitSelected = CGuiObj.selectedOptions[i].value.cguiComponentPartNumber;
                        cableLength = CGuiObj.selectedOptions[i].value.userInput;
                        break;

                    default:
                        if (CGuiObj.selectedOptions[i].value.cguiComponentPrice != '' &&  !isNaN(CGuiObj.selectedOptions[i].value.cguiComponentPrice))
                            currentConditionsSet.misc.push(CGuiObj.selectedOptions[i].value.cguiComponentPrice);
                        break;
                }
            }

            return currentConditionsSet;
        }

        function getActiveStaticConditions(CGuiObj) {

            var staticConditionsToApply = {
                connectors: [],
                boot_type: [],
                cable: []
            };

            currentSelection = currentSelectionSetForConditions(CGuiObj, 2);

            // Setup static conds
            var currentCableId = currentSelection.glassType+currentSelection.jacketType;
            var activeGlassTypeId = currentSelection.glassType;

            for (var i=0; i<CGuiObj.models.conditions.staticConds.length; i++) {

                // Setup static conds for connector, for ease
                if (CGuiObj.models.conditions.staticConds[i].for.toLowerCase() == 'connector') {

                    for (var key in CGuiObj.models.conditions.staticConds[i]) {
                        if (key.startsWith('subType-')) {
                            var extractedKeySet = key.split("-");
                            var cableIdToMatch = extractedKeySet[1].match(/\d+/g).map(Number);
                            var connectorNameToMatch = extractedKeySet[1].replace(/[0-9]/g, '');;

                            if (activeGlassTypeId.toString() == cableIdToMatch.toString()) {
                                staticConditionsToApply.connectors.push({
                                    wireKey: cableIdToMatch.toString(),
                                    connectorName: connectorNameToMatch,
                                    color:  CGuiObj.models.conditions.staticConds[i][key]
                                });
                            }
                        }
                    }
                }

                // Setup Boot static conds
                if (CGuiObj.models.conditions.staticConds[i].for.toLowerCase() == 'boot') {
                    for (var key in CGuiObj.models.conditions.staticConds[i]) {
                        if (key.startsWith('subType-')) {
                            var extractedKeySet = key.split("-");
                            if (extractedKeySet.length < 2)
                                continue;
                            var bootIdToMatch = extractedKeySet[1];
                            // var bootSampleId = extractedKeySet[2];


                            if (activeGlassTypeId.toString() == bootIdToMatch.toString()) {
                                staticConditionsToApply.boot_type.push({
                                    wireKey: cableIdToMatch.toString(),
                                    connectorName: connectorNameToMatch,
                                    color:  CGuiObj.models.conditions.staticConds[i][key]
                                });
                            }
                        }
                    }
                }

                // Setup Cable static conds
                if (CGuiObj.models.conditions.staticConds[i].for.toLowerCase() == 'cable') {
                    for (var key in CGuiObj.models.conditions.staticConds[i]) {
                        if (key.startsWith('subType-')) {
                            var extractedKeySet = key.split("-");

                            if (extractedKeySet.length < 2)
                                continue;

                            var cableIdToMatch = extractedKeySet[1].match(/\d+/g).map(Number);
                            var wireTypeToMatch = extractedKeySet[1].replace(/[0-9]/g, '').toLowerCase();

                            if (currentCableId.toString() == cableIdToMatch.toString()) {
                                staticConditionsToApply.cable.push({
                                    cableId: cableIdToMatch.toString(),
                                    wireType: wireTypeToMatch.toLowerCase(),
                                    color:  CGuiObj.models.conditions.staticConds[i][key]
                                });
                            }
                        }
                    }
                }


            }
            return staticConditionsToApply;
        }

        function updateConnectorMenuByStaticConditions(CGuiObj) {

            // Resetting Connector & Boot colors before applying conditions
            resetConnectorColorBeforeAplyingConditons();
            resetBootsBeforeAplyingConditons();

            // Grab the ol and connector left and right parents
            var $LeftConnectorWrap = $("ol#steps-customs").find("li[data-config-name='connector_a']").find('ul.sub-content>li');
            var $RightConnectorWrap = $("ol#steps-customs").find("li[data-config-name='connector_b']").find('ul.sub-content>li');
            var $BootWrap = $("ol#steps-customs").find("li[data-config-name='boot_type']").find('ul.sub-content>li');

            for (var j=0; j<CGui.CanvasToolObj.activeConds.staticConds.connector.length; j++) {
                transformConnectorColor(
                    CGui.CanvasToolObj.activeConds.staticConds.connector[j].connectorName,
                    CGui.CanvasToolObj.activeConds.staticConds.connector[j].color,
                    CGui.CanvasToolObj.activeConds.staticConds.connector[j].key
                    );
            }

            // Updating Boot conditions

            for (var j=0; j<CGui.CanvasToolObj.activeConds.staticConds.boot.length; j++) {
                for (var i=0; i<$BootWrap.length; i++) {

                    var bootPartNumberKey = CGui.CanvasToolObj.activeConds.staticConds.boot[j].bootPartNumberKey;
                    var colors = CGui.CanvasToolObj.activeConds.staticConds.boot[j].color;

                    if ($($BootWrap[i]).find('a').eq(0).data('cgui-component-part-number').toString() != bootPartNumberKey) {
                        continue;
                    }
                    updateBootColors($($BootWrap[i]).find('a').eq(0), colors[0], true)
                }
            }

            updateBootColorOptions(true);
            // Updating the menu link for submenus
            for (var i=0; i<$BootWrap.length; i++)
                updateMenuImageUrlColor($($BootWrap[i]).find('span.graphics-img img').eq(0));
        }

        function transformConnectorColor(connectorName, color, key) {
            var $ConnectorSet = [];
            var colorset = [];

            // This hack is to support arrays|strign for color options, although for connectors string is more likely
            if (typeof(color) == 'string' )
                colorset.push(color);
            else
                colorset = color;

            $ConnectorSet = $("ol#steps-customs").find("li[data-config-name='connector_a'], li[data-config-name='connector_b']").find('ul.sub-content>li');

            for(var j=0; j<$ConnectorSet.length; j++) {
                // COntinue if no match found
                if ($($ConnectorSet[j]).find('a').eq(0).data('cgui-component-name').toLowerCase().replace(/ /g,'') != connectorName) {
                    continue;
                }
                updateConnectorColors($($ConnectorSet[j]).find('a').eq(0), colorset, true);
                updateMenuImageUrlColor($($ConnectorSet[j]).find('span.graphics-img img').eq(0), colorset[0]);

            }

        }

        function applyCustomStaticConditions(CGuiObj) {

            if (CGuiObj.CanvasToolObj.wireType.type == 'simplex')
                hideUnibootIfSimplex(CGuiObj);
            else
                hideUnibootForSomeGroups();

        }

        function hideUnibootForSomeGroups() {
            if ( $('ol#steps-customs').find('li.parent-element').eq(0).data('group-name').toLowerCase().indexOf('jumpers') !== -1 ||  $('ol#steps-customs').find('li.parent-element').eq(0).data('group-name').toLowerCase().indexOf('test reference') !== -1 ) {
                // Showing connectors uniboot only in case of groups indoor jumpers & test reference cords
                hideUniBoots(false);
                return;
            }
            hideUniBoots();
        }

        function hideUniBoots(hide) {
        	if (hide === undefined)
        		hide=true;
            if (hide)
                $('ol#steps-customs').find('li[data-config-name="connector_a"], li[data-config-name="connector_b"]').find('a[data-cgui-component-name="LC UPC UNIBOOT"]').parent().addClass('hidden');
            else
                $('ol#steps-customs').find('li[data-config-name="connector_a"], li[data-config-name="connector_b"]').find('a[data-cgui-component-name="LC UPC UNIBOOT"]').parent().removeClass('hidden');
        }

        function hideUnibootIfSimplex(CGuiObj) {
            if (CGuiObj.CanvasToolObj.wireType.type != 'simplex')
                return;

            hideUniBoots();
        }

        function updateConnectorMenuByDBConditions(CGuiObj) {

            // Grab the ol and connector left and right parents
            var $LeftConnectorWrap = $("ol#steps-customs").find("li[data-config-name='connector_a']").find('ul.sub-content>li');
            var $RightConnectorWrap = $("ol#steps-customs").find("li[data-config-name='connector_b']").find('ul.sub-content>li');
            var $BootWrap = $("ol#steps-customs").find("li[data-config-name='boot_type']").find('ul.sub-content>li');

            var dbConditionsToApply = {
                connector_a: [],
                connector_b: [],
                boot_type: []
            };

            currentSelection = currentSelectionSetForConditions(CGuiObj, 3);

            // Go through each of the db conds
            var currentCableId = currentSelection.glassType+currentSelection.jacketType+currentSelection.fiberCount;
            for (var i=0; i<CGuiObj.models.conditions.db.length; i++) {
                if (currentCableId == CGuiObj.models.conditions.db[i].cable_id) {
                    for (var j=0; j<CGuiObj.models.conditions.db[i].cable_conditions.length; j++) {
                        var currentConditionForKey = Object.keys(CGuiObj.models.conditions.db[i].cable_conditions[j]);

                        // Applies for connector_a
                        if (currentConditionForKey == '952') {
                            dbConditionsToApply.connector_a.push(CGuiObj.models.conditions.db[i].cable_conditions[j]);
                            applyConditionsToConnectors(dbConditionsToApply, $LeftConnectorWrap, 'connector_a');
                        }

                        // Applies for connector_b
                        if (currentConditionForKey == '953') {
                            dbConditionsToApply.connector_b.push(CGuiObj.models.conditions.db[i].cable_conditions[j]);
                            applyConditionsToConnectors(dbConditionsToApply, $RightConnectorWrap, 'connector_b');
                        }

                        // Applies for connector_b
                        if (currentConditionForKey == '954') {
                            dbConditionsToApply.boot_type.push(CGuiObj.models.conditions.db[i].cable_conditions[j]);
                            applyConditionsToBoots(CGuiObj.models.conditions.db[i].cable_conditions[j][954], $BootWrap);
                        }

                    }
                }
            }

            //Updating boot menus
            updateBootColorOptions();
            // Updating the menu link for submenus
            for (var i=0; i<$BootWrap.length; i++)
                updateMenuImageUrlColor($($BootWrap[i]).find('span.graphics-img img').eq(0));

            return;
        }

        function applyConditionsToBoots(dbConditionsToApply, $BootWrap) {

            for (var i=0; i<$BootWrap.length; i++) {

                var appliesToComponentId = dbConditionsToApply.id;
                var colorSet = dbConditionsToApply.data;

                if ($($BootWrap[i]).find('a').eq(0).data('cgui-component-id').toString() != appliesToComponentId.toString()) {
                    continue;
                }

                updateBootColors($($BootWrap[i]).find('a').eq(0), colorSet);

            }

        }

        /**
         * Inverse is to allow showing/hiding the boots, is used  to process
         * static & dynamic condition.
         */
        function updateBootColors($elem, colors, inverse) {
        	if (inverse === undefined)
        		inverse=false;

            var canvasImageUrlSet = $elem.data('canvas-image');
            var colorSet = colors;
            if (typeof(colors) == 'string' )
                colorSet.push(colors);

            if (colorSet.length == 0)
                return;

            if (canvasImageUrlSet.length == 0) {
                hideConnectorElem($elem);// this representsany elem, boot or connectors
            }
            var matchCount =0;
            for (var k=0; k<colorSet.length; k++) {

                for (var i=0; i<canvasImageUrlSet.length; i++) {

                    var currentCanvasImage = strToArrImgUrlName(canvasImageUrlSet[i].img);

                    if (inverse == true) {

                        // SHow that very element and hide rest
                        if ( colorSet.indexOf(currentCanvasImage.color) == -1) {
                            canvasImageUrlSet[i].status = 0;
                            $($elem).parent().find("div.color-optionDiv button[data-color='"+colorSet[k]+"']").addClass("hidden");
                        }

                    } else {
                        if ( currentCanvasImage.color == colorSet[k]) {
                            canvasImageUrlSet[i].status = 0;
                            $($elem).parent().find("div.color-optionDiv button[data-color='"+colorSet[k]+"']").addClass("hidden");
                        }

                    };

                } // ENd of for loop

            }

            var inactive=0;
            for (var i=0; i<canvasImageUrlSet.length; i++) {
                if (canvasImageUrlSet[i].status == 0)
                    inactive++;
            }

            if (inactive == canvasImageUrlSet.length) {
                hideConnectorElem($elem);
            }

            $elem.attr('data-canvas-image', JSON.stringify(canvasImageUrlSet));
        }

        function loadImage(url) {
            var newImage = new Image();
            newImage.onload = function() {
            }
            newImage.src = url;
            return;
        }

        function findFirstActiveImage($elem) {

            var canvasImageUrlSet = $elem.data('canvas-image');
            if (canvasImageUrlSet == null)
                return;
            // //Find the first active newColorUrl
            var newColorUrl = '';
            if (canvasImageUrlSet.length >= 1)
                newColorUrl = canvasImageUrlSet[0].img;
            for (var i=0; i<canvasImageUrlSet.length; i++) {
                if (parseInt(canvasImageUrlSet[i].status) == 1) {
                    newColorUrl = canvasImageUrlSet[i].img;
                    break;
                }
            }

            return newColorUrl;
        }

        function updateBootColorOptions(inverse) {
        	if (inverse === undefined)
        		inverse=false;

            $BootElems = $("ol#steps-customs li[data-config-name='boot_type'] ul.sub-content li");
            for(var i=0; i<$BootElems.length; i++) {
                var canvasImageUrlSet = $($BootElems[i]).find('a').eq(0).data('canvas-image');
                var $ColorOptionsDiv = $($BootElems[i]).find('a').eq(0).parent().find('div.color-optionDiv');

                // //Find the first active newColorUrl
                var newColorUrl = '';
                var hideAllFlag = true;
                for (var j=0; j<canvasImageUrlSet.length; j++) {
                    var currentColorSet = strToArrImgUrlName(canvasImageUrlSet[j].img);
                    if (parseInt(canvasImageUrlSet[j].status) == 0) {
                        $($ColorOptionsDiv).find("button[data-color='"+currentColorSet.color+"']").addClass("hidden");
                    }
                    else{
                        hideAllFlag = false;
                        $($ColorOptionsDiv).find('button[data-color="'+currentColorSet.color+'"]').removeClass("hidden");
                    }
                }

                // Hide all elements if there are all hidden elements
                if (hideAllFlag == true)
                    hideConnectorElem($($BootElems[i]).find('a').eq(0));

            }
            return;

        }

        function updateBootMenuIcons() {
            var $BootWrap = $("ol#steps-customs").find("li[data-config-name='boot_type_a'], li[data-config-name='boot_type_b']").find('ul.sub-content>li');
            // Updating the menu link for submenus
            for (var i=0; i<$BootWrap.length; i++)
                updateMenuImageUrlColor($($BootWrap[i]).find('span.graphics-img img').eq(0));
        }

        // No need for colorset
        function updateMenuImageUrlColor($elem, colorSet) {
        	if (colorSet === undefined)
        		colorSet=false;

            var newColorUrl = findFirstActiveImage($elem.parents('a').eq(0));

            if (newColorUrl === undefined)
                return;

            var origUrl = $elem.attr('src').split("/");
            origUrlSet = strToArrImgUrlName(origUrl[origUrl.length-1]);

            origUrl.length = origUrl.length-1;
            var origUrlStr = origUrl.join("/");

            var newMenuImage = origUrlStr+"/"+newColorUrl;
            loadImage(newMenuImage);
            $elem.attr('src', newMenuImage);
            return;
        }

        function applyStaticConditionsToConnectors(conditionsToApply, $ConnectorSet) {
            for (var i=0; i<conditionsToApply.connectors.length; i++) {

                var connectorName = conditionsToApply.connectors[i].connectorName.toLowerCase();
                var colorSet = conditionsToApply.connectors[i].color;

                for(var j=0; j<$ConnectorSet.length; j++) {
                    // COntinue if no match found
                    if ($($ConnectorSet[j]).find('a').eq(0).data('cgui-component-name').toLowerCase().replace(/ /g,'') != connectorName.toString()) {
                        continue;
                    }
                    updateConnectorColorsForStaticConds($($ConnectorSet[j]).find('a').eq(0), colorSet);

                    updateMenuImageUrlColor($($ConnectorSet[j]).find('span.graphics-img img').eq(0), colorSet);

                }

            }
        }

        function applyConditionsToConnectors(dbConditionsToApply, $ConnectorSet, for_connector) {

            if (for_connector =='connector_a')
                var maxLimit = dbConditionsToApply.connector_a.length;
            else
                var maxLimit = dbConditionsToApply.connector_b.length;

            for (var i=0; i<maxLimit; i++) {

                if (for_connector == 'connector_a') {
                    var appliesToConnectorId = dbConditionsToApply.connector_a[i][952].id;
                    var colorSet = dbConditionsToApply.connector_a[i][952].data;
                }else{
                    var appliesToConnectorId = dbConditionsToApply.connector_b[i][953].id;
                    var colorSet = dbConditionsToApply.connector_b[i][953].data;
                }
                for(var j=0; j<$ConnectorSet.length; j++) {
                    // COntinue if no match found
                    if ($($ConnectorSet[j]).find('a').eq(0).data('cgui-component-id') != appliesToConnectorId.toString()) {
                        continue;
                    }

                    updateConnectorColors($($ConnectorSet[j]).find('a').eq(0), colorSet);
                    updateMenuImageUrlColor($($ConnectorSet[j]).find('span.graphics-img img').eq(0), colorSet[0]);

                }

            }

        }

        function hideConnectorElem($elem, status) {
        	if (status === undefined)
        		status=true;
            if (status === true) {
                $elem.parent().addClass('hidden');
                return;
            }
            $elem.parent().removeClass('hidden');

        }

        function generateConnectorCanvasImageUrl(connectorName, color) {
            return MEDIA_DIR+'connector_'+connectorName.toLowerCase()+'_'+color+'.png';
        }

        function resetBootsBeforeAplyingConditons() {

            $BootSet = $("ol#steps-customs").find("li[data-config-name='boot_type'] ul.sub-content>li>a");

            for(var j=0; j<$BootSet.length; j++) {

                // Remove hidden class if it exists
                $($BootSet[j]).parent().removeClass('hidden');

                var canvasImageUrlSet = $($BootSet[j]).data('canvas-image');

                if (canvasImageUrlSet  == null)
                    continue;
                for (var i=0; i<canvasImageUrlSet.length; i++) {
                    canvasImageUrlSet[i].status = 1;
                }
                $($BootSet[j]).attr('data-canvas-image', JSON.stringify(canvasImageUrlSet));

                $($BootSet[j]).parent().find("div.color-optionDiv button").removeClass("hidden");
            }


        }

        function resetConnectorColorBeforeAplyingConditons() {

            $ConnectorSet = $("ol#steps-customs").find("li[data-config-name='connector_a'] ul.sub-content>li>a,li[data-config-name='connector_b'] ul.sub-content>li>a");

            for(var j=0; j<$ConnectorSet.length; j++) {

                // Remove hidden class if it exists
                $($ConnectorSet[j]).parent().removeClass('hidden');

                var canvasImageUrlSet = $($ConnectorSet[j]).data('canvas-image');

                if (canvasImageUrlSet  == null)
                    continue;
                for (var i=0; i<canvasImageUrlSet.length; i++) {
                    canvasImageUrlSet[i].status = 1;
                }
                $($ConnectorSet[j]).attr('data-canvas-image', JSON.stringify(canvasImageUrlSet));

            }
        }

        function updateConnectorColors($elem, colorSet, inverse) {
        	if (inverse === undefined)
        		inverse=false;

            var defaultStatus = 1;
            if (inverse == true)
                defaultStatus = 0;
            $elem.parent().removeClass('hidden');
            var canvasImageUrlSet = $elem.data('canvas-image');

            if (canvasImageUrlSet == null){
                hideConnectorElem($elem);
                return;
            }

            // Return if no color set specified for filtering
            if (colorSet.length == 0)
                return;


            if ( canvasImageUrlSet !== null && canvasImageUrlSet.length == 0) {
                hideConnectorElem($elem);
            }

            var matched = 0;
            for (var k=0; k<colorSet.length; k++) {

                for (var i=0; i<canvasImageUrlSet.length; i++) {
                    var currentCanvasImage = strToArrImgUrlName(canvasImageUrlSet[i].img);

                    if (inverse == true) {

                        // SHow that very element and hide rest
                        if ( currentCanvasImage.color == colorSet[k]) {
                            canvasImageUrlSet[i].status = 1;
                        }
                        else {
                            canvasImageUrlSet[i].status = 0;
                        }

                    } else {
                        if ( currentCanvasImage.color == colorSet[k]) {
                            canvasImageUrlSet[i].status = 0;
                        }
                    };
                }

            }

            if (canvasImageUrlSet.length == 0) {
                hideConnectorElem($elem);
            }

            var inactive=0;
            for (var i=0; i<canvasImageUrlSet.length; i++) {
                if (canvasImageUrlSet[i].status == 0)
                    inactive++;
            }

            if (inactive == canvasImageUrlSet.length) {
                hideConnectorElem($elem);
            }

            $elem.attr('data-canvas-image', JSON.stringify(canvasImageUrlSet));
        }

        function refreshElem($elem, type) {
        	if (type === undefined)
        		type='connector';
            var canvasImageUrlSet = $elem.data('canvas-image');
            if (canvasImageUrlSet.length == 0) {

                if (type == 'connector')
                    hideConnectorElem($elem);

                if (type == 'boot') {
                    hideConnectorElem($elem);
                }
                return;
            }

            var firstActiveIndex = -1;
            for (var i=0; i<canvasImageUrlSet.length; i++) {
                if (canvasImageUrlSet[i].status == 0) {
                    firstActiveIndex = i;
                    break;
                }
            }

            if (firstActiveIndex == -1) {
                // Not found hide the element
                if (type == 'connector')
                    hideConnectorElem($elem);
            } else {
                // hide only the specifid element found
                if (type == 'connector') {
                    $elem.find('span.graphics-img img').eq(0).atr('src', '');
                }
            }

        }

        function strToArrImgUrlName(url) {
            var bootNameArr = url.split("_");
            var extArr = bootNameArr[bootNameArr.length-1].split(".");
            var bootNameSet = {
                fullUrl: '',
                urlBeforeColor: '',
                arr: [],
                color: '',
                ext: '',
            };

            for (var i=0; i<bootNameArr.length-1; i++) {
                bootNameSet.arr.push(bootNameArr[i]);
                bootNameSet.fullUrl += bootNameArr[i] + "_";
            }

            bootNameSet.urlBeforeColor = bootNameSet.fullUrl.slice(0, -1);
            bootNameSet.color = extArr[0];
            bootNameSet.ext = extArr[1];

            bootNameSet.fullUrl += extArr[0]+"."+extArr[1];

            return bootNameSet;
        }

    }

    function get2D( num ) {
        return ( num.toString().length < 2 ? "0"+num : num ).toString();
    }

}());

function lockCanvas(lock) {
	if (lock === undefined)
		lock=true;
    if (lock == true)
        $('ol#steps-customs').css('pointer-events', 'none');
    else
        $('ol#steps-customs').css('pointer-events', 'auto');
}

function showLoader(show) {
	if (show === undefined)
		show=true;
    if (show == true){
        $('#loader-divsec').removeClass("hidden");
        $('ol#steps-customs').css('pointer-events', 'none');
    }else {
        $('#loader-divsec').addClass("hidden");
        $('ol#steps-customs').css('pointer-events', 'auto');
    }
}

var connectorMetaObj;

function sleep(milliseconds) {
    var start = new Date().getTime();
    for (var i = 0; i < 1e7; i++) {
        if ((new Date().getTime() - start) > milliseconds){
            break;
        }
    }
}

function getRandomArbitrary(min, max) {
  return Math.random() * (max - min) + min;
}
