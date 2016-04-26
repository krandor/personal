describe('User', function(){
  it('should create user based on provided data', function(){
    var user = new User({name: "Gob", screenname: "GobTheMagnificent", status:"Online"});

    expect(user).toBeDefined();
  });
});
