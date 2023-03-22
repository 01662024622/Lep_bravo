// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

// Pie Chart Example

var data=[];
$.ajax({
  type: "GET",
  url: "/pie/avg/",
  success: function(response)
  {
    var reliability = response.reliability.reduce(function(a, b){
        return a + b;
    }, 0);
    var extracted = response.extracted.reduce(function(a, b){
        return a + b*b;
    }, 0);
    var expo= extracted/(extracted+reliability);
    console.log(extracted);
    console.log(reliability);
    $('#sumall').text(expo*10);

    data=response.extracted
    var ctx = document.getElementById("myPieChart");
    var myPieChart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ["Sevqual", "Infoqual", "User",'Benefit'],
        datasets: [{
          data: data,
          backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc','#ffff66'],
          hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf','#ffff00'],
          hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
      },
      options: {
        maintainAspectRatio: false,
        tooltips: {
          backgroundColor: "rgb(255,255,255)",
          bodyFontColor: "#858796",
          borderColor: '#dddfeb',
          borderWidth: 1,
          xPadding: 15,
          yPadding: 15,
          displayColors: false,
          caretPadding: 10,
        },
        legend: {
          display: false
        },
        cutoutPercentage: 80,
      },
    });
  },
  error: function (xhr, ajaxOptions, thrownError) {
    toastr.error(thrownError);
  }
});
// Bar Chart Example
