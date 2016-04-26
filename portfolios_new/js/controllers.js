var app = angular.module('portfolioAdmin', []);

app.controller('DashboardController', function($scope) {
  $scope.user = new User({
    name:"krandor",
    screenname:"krandor",
    avatarURI:"images/users/1_sm.jpg",
    status: "Offline",
    messages: [], comments: [],
    downloads: [{project:{name: "Highlander", description:"In-house-built CRM system"}, downloadCount:3},
                {project:{name:"CyberXMPP", description:"In-office chat program."}, downloadCount:3},
                {project:{name:"Eve-Online Mining Share Calculator", description:"Tool to calculate monitary distribution of mining profits for the MMORPG 'Eve-Online'"}, downloadCount:3}],
    saves: 2,
    projects: [
      {name: "Highlander", description:"In-house-built CRM system"},
      {name:"CyberXMPP", description:"In-office chat program."},
      {name:"Eve-Online Mining Share Calculator", description:"Tool to calculate monitary distribution of mining profits for the MMORPG 'Eve-Online'"},
      {name:"Service Sentinel", description:"Service Suit to monitor Servers and Services"},
      {name:"PortfolioAdmin", description:"Application to manage your professional portfolio."}
    ]
  });

});
