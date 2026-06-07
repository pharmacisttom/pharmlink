import React, { useState, useEffect } from 'react';
import { ClockIcon, CheckCircleIcon, ExclamationTriangleIcon, ArrowPathIcon } from '@heroicons/react/24/outline'; // Assuming you have heroicons

const Dashboard = () => {
  // Mock data for "Pending Returns"
  const [pendingReturns, setPendingReturns] = useState([
    {
      id: 1,
      ticket_id: 1042,
      drug_name: 'Paracetamol 500mg',
      provider: 'Central Provincial Hospital',
      quantity: 500,
      urgency_level: 'High',
      status: 'Received',
      received_date: '2023-10-25',
      overdue: true,
    },
    {
      id: 2,
      ticket_id: 1045,
      drug_name: 'Amoxicillin 250mg',
      provider: 'North District Hospital',
      quantity: 200,
      urgency_level: 'Medium',
      status: 'Received',
      received_date: '2023-10-28',
      overdue: false,
    },
    {
      id: 3,
      ticket_id: 1048,
      drug_name: 'Adrenaline 1mg/ml',
      provider: 'City General Hospital',
      quantity: 50,
      urgency_level: 'Emergency',
      status: 'Received',
      received_date: '2023-10-29',
      overdue: false,
    }
  ]);

  const getUrgencyBadge = (level) => {
    switch (level) {
      case 'Emergency':
        return <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200"><ExclamationTriangleIcon className="w-3 h-3 mr-1" /> Emergency</span>;
      case 'High':
        return <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">High</span>;
      case 'Medium':
        return <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">Medium</span>;
      case 'Low':
        return <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">Low</span>;
      default:
        return null;
    }
  };

  return (
    <div className="min-h-screen bg-slate-50 p-8 font-sans">
      <div className="max-w-7xl mx-auto space-y-8">
        
        <header className="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
          <div>
            <h1 className="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">PharmaLink Dashboard</h1>
            <p className="text-slate-500 mt-1 text-sm font-medium">Inter-Hospital Medication Tracking</p>
          </div>
          <div className="flex gap-4">
            <div className="flex flex-col items-end">
              <span className="text-sm font-semibold text-slate-700">Requester Hub</span>
              <span className="text-xs text-slate-400">Community Hospital Alpha</span>
            </div>
            <div className="h-10 w-10 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 shadow-md flex items-center justify-center text-white font-bold">
              A
            </div>
          </div>
        </header>

        <main>
          <div className="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div className="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
              <h2 className="text-xl font-semibold text-slate-800 flex items-center gap-2">
                <ClockIcon className="w-5 h-5 text-indigo-500" />
                Pending Returns
              </h2>
              <button className="text-sm px-4 py-2 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 transition-colors flex items-center gap-2 text-slate-600 font-medium">
                <ArrowPathIcon className="w-4 h-4" /> Refresh
              </button>
            </div>
            
            <div className="overflow-x-auto">
              <table className="w-full text-left border-collapse">
                <thead>
                  <tr className="bg-slate-50 border-b border-slate-100 text-sm uppercase tracking-wider text-slate-500 font-semibold">
                    <th className="p-4 pl-6">Ticket ID</th>
                    <th className="p-4">Drug Details</th>
                    <th className="p-4">Provider</th>
                    <th className="p-4">Qty</th>
                    <th className="p-4">Urgency</th>
                    <th className="p-4">Status</th>
                    <th className="p-4 pr-6 text-right">Action</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-100">
                  {pendingReturns.map((item) => (
                    <tr key={item.id} className="hover:bg-slate-50/80 transition-colors group">
                      <td className="p-4 pl-6 font-mono text-sm text-indigo-600 font-medium">#{item.ticket_id}</td>
                      <td className="p-4">
                        <div className="font-semibold text-slate-800">{item.drug_name}</div>
                        <div className="text-xs text-slate-500 mt-0.5">Rcvd: {item.received_date}</div>
                      </td>
                      <td className="p-4 text-sm text-slate-600">{item.provider}</td>
                      <td className="p-4 font-medium text-slate-700">{item.quantity}</td>
                      <td className="p-4">
                        {getUrgencyBadge(item.urgency_level)}
                      </td>
                      <td className="p-4">
                        {item.overdue ? (
                          <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-600 border border-rose-100">
                            Overdue
                          </span>
                        ) : (
                          <span className="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                            <span className="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Pending
                          </span>
                        )}
                      </td>
                      <td className="p-4 pr-6 text-right">
                        <button className="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg shadow-sm hover:bg-indigo-700 hover:shadow transition-all active:scale-95">
                          Initiate Return
                        </button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
              {pendingReturns.length === 0 && (
                <div className="p-12 text-center text-slate-500 flex flex-col items-center">
                  <CheckCircleIcon className="w-12 h-12 text-slate-300 mb-3" />
                  <p>All clear! No pending returns at the moment.</p>
                </div>
              )}
            </div>
          </div>
        </main>
        
      </div>
    </div>
  );
};

export default Dashboard;
