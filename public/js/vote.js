
function update(nbre,Elt){
    Elt.innerHTML="";
    Elt.textContent = nbre ;

}

let bodyElt = document.body;
let increaseElt = document.getElementById("countLike");
let decreaseElt = document.getElementById("countDislike");
let textLike = document.getElementById("like");
let textDislike = document.getElementById("dislike");
let plusElt = document.getElementById("add");
let minusElt= document.getElementById("remove");


plusElt.addEventListener("click",function (e) {
    e.preventDefault();

    let nbre = increaseElt.textContent;

    let link = Routing.generate('vote',{somme: nbre});

    fetch(`${link}`)
        .then(response => response.json())
        .then(data => {
            update(data["ajout"],increaseElt);
            textLike.textContent = increaseElt.textContent <= 1? "Like": "Likes";
        })
        .catch(error => console.log(error))
        .finally(() => console.log('fini'));

})

minusElt.addEventListener("click",function (e) {
    e.preventDefault();
    let nbre = decreaseElt.textContent;

    let link = Routing.generate('vote',{somme: nbre});

    fetch(`${link}`)
        .then(response => response.json())
        .then(data => {

            update(data["ajout"],decreaseElt);
            textDislike.textContent = decreaseElt.textContent <= 1 ? "Dislike": "Dislikes";
        })
        .catch(error => console.log(error))
        .finally(() => console.log('fini'));

})