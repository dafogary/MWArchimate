document.addEventListener('DOMContentLoaded', () => {
  console.log("DOMContentLoaded event fired");
  document.querySelectorAll('.mwarchimate-container').forEach(container => {
    const cyDiv = document.createElement('div');
    cyDiv.style.width = '100%';
    cyDiv.style.height = '600px';
    container.appendChild(cyDiv);

    console.log("Cytoscape:", cytoscape);
    console.log("Container:", cyDiv);

    cytoscape({
      container: cyDiv,
      elements: [
        { data: { id: 'a', label: 'Node A' } },
        { data: { id: 'b', label: 'Node B' } },
        { data: { id: 'ab', source: 'a', target: 'b', label: 'Edge AB' } }
      ],
      style: [
        {
          selector: 'node',
          style: {
            'label': 'data(label)'
          }
        },
        {
          selector: 'edge',
          style: {
            'label': 'data(label)'
          }
        }
      ],
      layout: {
        name: 'grid'
      }
    });
  });
});