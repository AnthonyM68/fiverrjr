
import 'jquery'; // Importer jQuery (global)
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM fully loaded and parsed: app.js');
    $( function() {
        $( ".widget button" )
          .eq( 0 ).button()
          .end().eq( 1 ).button( {
            icon: "ui-icon-gear",
            showLabel: false
          } ).end().eq( 2 ).button( {
            icon: "ui-icon-gear"
          } ).end().eq( 3 ).button( {
            icon: "ui-icon-gear",
            iconPosition: "end"
          } ).end().eq( 4 ).button( {
            icon: "ui-icon-gear",
            iconPosition: "top"
          } ).end().eq( 5 ).button( {
            icon: "ui-icon-gear",
            iconPosition: "bottom"
          } );
      } );
});
