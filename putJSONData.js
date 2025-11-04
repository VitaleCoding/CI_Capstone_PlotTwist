// putJSONData.js
const putJSONData = async (data) => {
  const binId = "68db3f4bae596e708f009e5c";
  const apiKey = "$2a$10$R1S9oi04F7AYLcy30BU37eHzClLDoyFBvYWdA3OZBYBm7IixjYbn.";
  const url = `https://api.jsonbin.io/v3/b/${binId}`;

  const response = await fetch(url, {
    method: "PUT",
    headers: {
      "X-Master-Key": apiKey,
      "Content-Type": "application/json"
    },
    body: JSON.stringify(data) // direct array, not wrapped in "record"
  });

  return response.ok;
};
