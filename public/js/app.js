async function postJSON(url,data){ 
    const res=await fetch(url,{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify(data)}); 
        return res.json(); }

async function assignTask(id,assignee){ 
    const r=await postJSON('/src/api/assign_task.php',{taskId:id,assigneeId:assignee}); 
    if(r.success) location.reload(); 
    else alert(r.error||'Error'); }

async function changeStatus(id,newStatus){ 
    const r=await postJSON('/src/api/change_status.php',{taskId:id,newStatus:newStatus}); 
    if(r.success) location.reload(); 
    else alert(r.error||'Error'); }
    
