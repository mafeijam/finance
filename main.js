$('.overlay').click(function(){
   $(this).fadeOut()
})

$('img').click(function(){
   $('.overlay').fadeIn().children('img').attr('src', $(this).attr('src')).css('display', 'flex')
})

var dt = $('#datatable').DataTable({
   lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'ALL']],
   pageLength: -1,
   columnDefs: [
     {targets: 'no-sort', orderable: false},
     {targets: [2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19], searchable: false}
   ],
   dom: '<"col-md-6"l><"col-md-6"f><"col-md-3"i><"col-md-9"p>t',
   scrollY: '630px',
   scrollCollapse: true
})

$('.refresh-one').click(function(){
   var id = $(this).data('id')
   var price = $('#'+id)
   var change = price.next()
   var percent = change.next()
   var $this = $(this)
   $(this).addClass('rotate')
   $(this).on('animationend', function(){
      $this.removeClass('rotate')
   })
   $.getJSON('refresh.php').done(function(d){
      var oldprice = price.text().trim()

      if (oldprice != d[id].price) {
         if (oldprice > d[id].price) {
            price.addClass('down')
         } else {
            price.addClass('up')
         }

         setTimeout(function(){
            price.html(d[id].price)
            change.html(d[id].change)
            percent.html(d[id].percent)
            setColor(d[id].change, change, percent)
         }, 300)
      }
      price.on('animationend', function(){
         price.removeClass('down').removeClass('up')
      })
   })
})

var ids = []
$('.refresh').each(function(k, v){
   ids.push($(v).children('i').data('id'))
})

var date = new Date()
var now = date.getHours()+date.getMinutes()/60

setInterval(function(){
   var date = new Date()
   now = date.getHours()+date.getMinutes()/60
}, 900000)

$('#refresh-all').click(function(){
   $.getJSON('refresh.php').done(function(d){
      $.each(ids, function(k, id){
         refreshAll(id, d)
      })
   })

   setLastUpdate($('#lastupdate'))
})

if ($.inArray(date.getDay(), [1, 2, 3, 4, 5]) != '-1') {
   var refresh = setInterval(function(){
      $.getJSON('refresh.php').done(function(d){
         $.each(ids, function(k, id){
            refreshAll(id, d)
            if (k+1 == ids.length) {
               rebuild()
            }
         })
      })

      setLastUpdate($('#lastupdate'))

      now = date.getHours()+date.getMinutes()/60

      if (now >= 16.5 || now <= 9) {
         clearInterval(refresh)
      }

   }, 900000)

   if (now >= 16.5 || now <= 9) {
      clearInterval(refresh)
   }
}

setLastUpdate($('#lastupdate'))

$('.favorite').click(function(){
   var id = $(this).parent().siblings().last().children().data('id')
   var s = $(this)
   $.getJSON('ajax-favorite.php', {f: id}).done(function(d){
      if (s.hasClass('fa-star')) {
         s.removeClass('fa-star').addClass('fa-star-o').css('color', '#08192D').html('<span style="font-size: 0;">1</span>')
         rebuild()
      } else {
         s.removeClass('fa-star-o').addClass('fa-star').css('color', '#F7D94C').html('<span style="font-size: 0;">0</span>')
         rebuild()
      }
   })
   console.log('add')
})

function setColor(value, change, percent) {
   if (value > 0) {
      change.css('color', '#227D51')
      percent.css('color', '#227D51')
   } else if (value < 0) {
      change.css('color', '#CB1B45')
      percent.css('color', '#CB1B45')
   } else {
      change.css('color', '#08192D')
      percent.css('color', '#08192D')
   }
}

function setLastUpdate(target) {
   var d = new Date()
   var m = String(d.getMinutes())

   if (m.length == 1) {
      m = '0'+m
   }

   var lastupdate = d.getHours() + ':' + m

   target.html('last update at <strong>'+lastupdate+'</strong>')
}

function rebuild() {
   $('#datatable').DataTable({
      lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'ALL']],
      pageLength: -1,
      columnDefs: [
        {targets: 'no-sort', orderable: false},
        {targets: [2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19], searchable: false}
      ],
      dom: '<"col-md-6"l><"col-md-6"f><"col-md-3"i><"col-md-9"p>t',
      scrollY: '630px',
      scrollCollapse: true,
      destroy: true
   })
}

function refreshAll(id, d) {
   var price = $('#'+id)
   var change = price.next()
   var percent = change.next()
   var oldprice = price.text().trim()

   if (oldprice != d[id].price) {
      if (oldprice > d[id].price) {
         price.addClass('down')
      } else {
         price.addClass('up')
      }
      setTimeout(function(){
         price.html(d[id].price)
         change.html(d[id].change)
         percent.html(d[id].percent)
         setColor(d[id].change, change, percent)
      }, 300)
   }

   price.on('animationend', function(){
      price.removeClass('down').removeClass('up')
   })
}

$('#add').click(function(){
   console.log($('#addcode').val())
})

$('.remove').click(function(){
   var id = $(this).data('id')
   var row = $(this).parents('tr')
   $.get('ajax-remove.php', {r: id}).done(function(d){
      dt.row(row).remove().draw()
      console.log(d)
   })
})