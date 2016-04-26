var User = function(user)
{
  var self = this;
  self.name = user.name;
  self.screenname = user.screenname;
  self.status = user.status;
  self.avatarURI = user.avatarURI;
  self.messages = user.messages;
  self.downloads = user.downloads;
  self.comments = user.comments;
  self.saves = user.saves;
  self.projects = user.projects;

  //functions
  self.totalNumOfDownloads = function()
  {
    var num = 0;
    self.downloads.forEach(function(download){
        num = num + download.downloadCount;
    });
    return num;
  };

  self.statusCss = function()
  {
      var css = "text-muted";
      switch (self.status) {
        case "Online":
          css = "text-success";
          break;
        case "Away":
          css = "text-warning";
          break;
        case "Offline":
          css = "text-muted";
          break;
        default:
          break;
      }
      
      return css;
  }
}
