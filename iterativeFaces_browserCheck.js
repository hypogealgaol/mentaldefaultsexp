//setup
$( ".circle" ).each( function() {
 
    var radius = $( this ).width() / 2,
        left = $( this ).position().left,
        top = $( this ).position().top;
 
    $( this ).data( { 
        
        "radius": radius, 
        "left": left, 
        "top": top,
        "clicked": false
        
    } );
    
} );

var startingMarkerAngle = 0; // start at top of circle
//var element=document.getElementById('testCircle');
var testCircle = $('#testCircle');
//var testCircle = document.getElementById("testCircle");
var testCircleRadius = 100;
var testCircleLeft = 190;
var testCircleRight = 190;
var testCircleTop = 190;
var testCircleHeight = 200;
var testCircleWidth = 200;

    
var expansion = 2; // number of pixels to expand or contract the circle on each degree
var numSteps = 360/2; // a different step every 2 degrees
var degreesPerStep = 360/numSteps;


//randomX = Math.round(Math.random()*);
function rotateAnnotationCropper_circleExpansion(offsetSelector, xCoordinate, yCoordinate, cropper, expansion){
        //alert(offsetSelector.left);
        var x = xCoordinate - offsetSelector.offset().left - offsetSelector.width()/2;
        var y = -1*(yCoordinate - offsetSelector.offset().top - offsetSelector.height()/2);
        var theta = Math.atan2(y,x)*(180/Math.PI);        


        var cssDegs = convertThetaToCssDegs(theta);
        var rotate = 'rotate(' +cssDegs + 'deg)';
        cropper.css({'-moz-transform': rotate, 'transform' : rotate, '-webkit-transform': rotate, '-ms-transform': rotate});
        $('body').on('mouseup', function(event){ $('body').unbind('mousemove')});
        //return cssDegs;
        output =  cssDegs;
        output = (output % 360) + 1;
        if (output < 0) {
        output = 360+output;// since angles go from 0 to 270 and then -90 back to 0
    }
    expand = Math.round(output/degreesPerStep);
    if (expand < 1) {
        expand = 1;    
    }

    if (expand>(numSteps/2)) {
        expand = numSteps - (expand - 1);
    } 

    expand = expand * expansion; // multiply the raw number by the number of pixels each degree step should add
    
    d3.select('#expansionNum').text(expand);
    circleStats = expandCircle(testCircle, expand);

}

function convertThetaToCssDegs(theta){
    var cssDegs = 90 - theta;
    return cssDegs;
}


//move and expand
function expandCircle( circleElement, expand )  {
        
    var $this = $( circleElement ),
        circle = $this.data(),
        hoveredX = circle.left + circle.radius,
        hoveredY = circle.top + circle.radius;
        
    // change css properties
    $this.css({
        "width": ( 2 * circle.radius ) + expand + "px",
        "height": ( 2 * circle.radius ) + expand + "px",
        "left": circle.left - ( expand / 2 ) + "px",
        "top": circle.top - ( expand / 2 ) + "px",
        "border-top-left-radius": circle.radius + ( expand / 2 ) + "px",
        "border-top-right-radius": circle.radius + ( expand / 2 ) + "px",
        "border-bottom-left-radius": circle.radius + ( expand / 2 ) + "px",
        "border-bottom-right-radius": circle.radius + ( expand / 2 ) + "px",
        //"radius": circle.radius + ( expand / 2 ) + "px"
    
    });    

    
    return [circle.left, circle.top, circle.width, circle.height];
};
       

$(document).ready(function(){               
    // Initially randomize the starting position of the marker and therefore first face shown.
    var toggleMove = true; 
    var marker = document.getElementById("marker");
    marker.style.visibility = 'visible';

 
    var obj=document.getElementsByTagName('html')[0];
    var w=obj.offsetWidth;
    var h=obj.offsetHeight;

    var l=Math.floor(Math.random()*w);
    var t=Math.floor(Math.random()*h);  

    $('#marker').on('click', function(event){ // works!
        toggleMove = !toggleMove;
        if (toggleMove) {$('#marker').css({'background': 'lime'});

        } else {$('#marker').css({'background': 'red'});};
    });

    $('body').on('click',function(event){ // works for the first click, but due to overlap doesn't work after shut off.
        if (!$(event.target).closest('#marker').length) {
            // $('#myDiv').hide();
            toggleMove = false;
            $('#marker').css({'background': 'red'});
        };
    });
    $('html').mousemove(function(event){        
        if (toggleMove){                
            degs = rotateAnnotationCropper_circleExpansion($('#innerCircle').parent(), event.pageX,event.pageY, $('#marker'),expansion);                
            }
        }); 
            
    // });                    

}); 
