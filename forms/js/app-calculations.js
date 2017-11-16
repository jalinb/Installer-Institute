function ff_mecp_action(element, action)
{
  switch (action) {
    case 'click':
    vals = '';
    opts =  JQuery("[name=\"ff_nm_mecp[]\"]");
    for (o = 0; o < opts.length; o++)
     if (opts[o].checked) {
      
       vals = opts[o].value;
     }
     vals1 = '';
     opts =  JQuery("[name=\"ff_nm_sbca[]\"]");
     for (o = 0; o < opts.length; o++)
       if (opts[o].checked) {
        
         vals1 = opts[o].value;
       }
       vals2 = '';
       opts =  JQuery("[name=\"ff_nm_loud_speaker[]\"]");
       for (o = 0; o < opts.length; o++)
         if (opts[o].checked) {
          
           vals2 = opts[o].value;
         }
         vals3 = '';
         opts =  JQuery("[name=\"ff_nm_interior_removal[]\"]");
         for (o = 0; o < opts.length; o++)
           if (opts[o].checked) {
            
             vals3 = opts[o].value;
           }

           var result_raw = 
           Number(vals) +
           Number(vals1)+ 
           Number(vals2)+
           Number(vals3);

           function addthousand(n) {
            var rx = /(\d+)(\d{3})/;
            return String(n).replace(/^\d+/, function(w) {
              while (rx.test(w)) {
                w = w.replace(rx, '$1,$2');
              }
              return w;
            });
          }
          
          var a = addthousand(result_raw);
          var b = a.split('.');
          ff_getElementByName('extra_total').value = b[0];

          break;
          default:;
    } // switch





    switch (action) {
      case 'click':
      vals4 = '';
      opts =  JQuery("[name=\"ff_nm_mecp[]\"]");
      for (o = 0; o < opts.length; o++)
       if (opts[o].checked) {
        
         vals4 = opts[o].value;
       }
       vals5 = '';
       opts =  JQuery("[name=\"ff_nm_sbca[]\"]");
       for (o = 0; o < opts.length; o++)
         if (opts[o].checked) {
          
           vals5 = opts[o].value;
         }
         vals6 = '';
         opts =  JQuery("[name=\"ff_nm_loud_speaker[]\"]");
         for (o = 0; o < opts.length; o++)
           if (opts[o].checked) {
            
             vals6 = opts[o].value;
           }
           vals7 = '';
           opts =  JQuery("[name=\"ff_nm_interior_removal[]\"]");
           for (o = 0; o < opts.length; o++)
             if (opts[o].checked) {
              
               vals7 = opts[o].value;
             }
             vals8 = '';
             opts =  JQuery("[name=\"ff_nm_course[]\"]");
             for (o = 0; o < opts.length; o++)
               if (opts[o].checked) {
                
                 vals8 = opts[o].value;
               }

               var result_raw = 
               Number(vals4) +
               Number(vals5)+ 
               Number(vals6)+
               Number(vals7)+
               Number(vals8);

               function addthousand(n) {
                var rx = /(\d+)(\d{3})/;
                return String(n).replace(/^\d+/, function(w) {
                  while (rx.test(w)) {
                    w = w.replace(rx, '$1,$2');
                  }
                  return w;
                });
              }
              
              var a = addthousand(result_raw);
              var b = a.split('.');
              ff_getElementByName('total_cost').value = b[0];

              break;
              default:;
    } // switch
} // close function