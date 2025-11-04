// getJSONData.js
const getJSONData = async () => {
  const binId = "68db3f4bae596e708f009e5c";
  const apiKey = "$2a$10$R1S9oi04F7AYLcy30BU37eHzClLDoyFBvYWdA3OZBYBm7IixjYbn.";
  const url = `https://api.jsonbin.io/v3/b/${binId}?meta=false`;

  const response = await fetch(url, {
    method: "GET",
    headers: {
      "X-Master-Key": apiKey,
      "Content-Type": "application/json"
    }
  });

  if (response.status !== 200) throw new Error("Cannot fetch data");
  let data = await response.json();
  return data; // now returns an array
};
